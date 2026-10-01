{{-- ═══════════════════════════════════
     GROUPED SPECIFICATIONS
═══════════════════════════════════ --}}
<p class="form-section-title">Grouped Specifications</p>

<div class="mb-4">
    <p class="text-muted mb-2" style="font-size:.82rem;">
        Create named groups (e.g. "Basic Info", "Electrical") and add key-value rows under each.
        These render as collapsible sections on the product page and are also auto-synced into the flat attributes for search and filtering.
    </p>

    <div id="attr-groups-container"></div>

    <button type="button" id="add-group-btn" class="btn btn-outline-primary btn-sm mt-1">
        <i class="fas fa-folder-plus me-1"></i> Add Group
    </button>
</div>

<style>
.attr-group-card {
    border: 1px solid #ebe9f1;
    border-radius: 0.428rem;
    margin-bottom: 1rem;
    background: #fafafa;
    overflow: hidden;
    transition: box-shadow .15s;
}
.attr-group-card:hover {
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.attr-group-header {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.65rem 0.85rem;
    background: #f3f2f7;
    border-bottom: 1px solid #ebe9f1;
}
.attr-group-header .group-name-inp {
    flex: 1;
    border: 1px solid #d8d6de;
    border-radius: 0.357rem;
    padding: 0.35rem 0.65rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #5e5873;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.attr-group-header .group-name-inp:focus {
    border-color: #7367f0;
    box-shadow: 0 3px 10px 0 rgba(115,103,240,0.2);
}
.attr-group-header .group-name-inp::placeholder {
    font-weight: 400;
    color: #b9b9c3;
}
.btn-rm-group {
    width: 32px; height: 32px; flex-shrink: 0;
    border: 1px solid #ffd4d4; background: #fff5f5; color: #ea5455;
    border-radius: 0.357rem; display: grid; place-items: center;
    cursor: pointer; font-size: 0.78rem; transition: 0.15s;
}
.btn-rm-group:hover { background: #ea5455; color: #fff; border-color: #ea5455; }

.attr-group-body { padding: 0.75rem 0.85rem; }

.attr-group-row {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 0.5rem;
    align-items: center;
    margin-bottom: 0.45rem;
}
.attr-group-row input {
    border: 1px solid #d8d6de;
    border-radius: 0.357rem;
    padding: 0.32rem 0.65rem;
    font-size: 0.82rem;
    color: #6e6b7b;
    background: #fff;
    outline: none;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
}
.attr-group-row input:focus {
    border-color: #7367f0;
    box-shadow: 0 3px 8px rgba(115,103,240,0.15);
}
.attr-group-row input::placeholder { color: #b9b9c3; }

.btn-rm-attr-row {
    width: 30px; height: 30px; flex-shrink: 0;
    border: 1px solid #ffd4d4; background: #fff5f5; color: #ea5455;
    border-radius: 0.357rem; display: grid; place-items: center;
    cursor: pointer; font-size: 0.72rem; transition: 0.15s;
}
.btn-rm-attr-row:hover { background: #ea5455; color: #fff; border-color: #ea5455; }

.btn-add-attr-row {
    font-size: 0.78rem; color: #7367f0; background: none; border: none;
    cursor: pointer; padding: 0; display: inline-flex; align-items: center; gap: 4px;
    margin-top: 0.25rem; font-weight: 600; transition: color .15s;
}
.btn-add-attr-row:hover { color: #5e50ee; }

.attr-row-header {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 0.5rem;
    margin-bottom: 0.3rem;
    padding: 0 0 0.2rem;
}
.attr-row-header span {
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.4px;
    color: #b9b9c3;
}
</style>

<script>
(function () {
    const container = document.getElementById('attr-groups-container');

    // ── Pre-populate existing groups (edit page) ──
    const existing = @json($product->getAttributeGroups());
    if (existing.length) {
        existing.forEach(g => addGroup(g.group, g.attrs));
    }

    document.getElementById('add-group-btn').addEventListener('click', () => addGroup());

    // ── Add a group card ──
    function addGroup(groupName = '', attrs = []) {
        const gIdx = container.querySelectorAll('.attr-group-card').length;

        const card = document.createElement('div');
        card.className = 'attr-group-card';

        // Header
        const header = document.createElement('div');
        header.className = 'attr-group-header';
        header.innerHTML = `
            <i class="fas fa-layer-group" style="color:#7367f0;font-size:0.8rem;flex-shrink:0;"></i>
            <input type="text"
                   class="group-name-inp"
                   name="attr_groups[${gIdx}][group]"
                   placeholder="Group name e.g. Basic Info, Electrical, Dimensions…"
                   value="${esc(groupName)}">
            <button type="button" class="btn-rm-group" title="Remove group">
                <i class="fas fa-trash-alt"></i>
            </button>`;
        header.querySelector('.btn-rm-group').addEventListener('click', () => {
            card.remove();
            reIndexGroups();
        });

        // Body
        const body = document.createElement('div');
        body.className = 'attr-group-body';

        // Column headers
        const colHeaders = document.createElement('div');
        colHeaders.className = 'attr-row-header';
        colHeaders.innerHTML = `
            <span>Key / Attribute</span>
            <span>Value</span>
            <span></span>`;
        body.appendChild(colHeaders);

        // Rows wrap
        const rowsWrap = document.createElement('div');
        rowsWrap.className = 'attr-rows-wrap';

        const rowData = attrs.length ? attrs : [{ key: '', value: '' }];
        rowData.forEach(attr => addAttrRow(rowsWrap, gIdx, attr.key, attr.value));

        // Add row button
        const addRowBtn = document.createElement('button');
        addRowBtn.type = 'button';
        addRowBtn.className = 'btn-add-attr-row';
        addRowBtn.innerHTML = `<i class="fas fa-plus"></i> Add Row`;
        addRowBtn.addEventListener('click', () => {
            addAttrRow(rowsWrap, getGroupIndex(card));
        });

        body.appendChild(rowsWrap);
        body.appendChild(addRowBtn);
        card.appendChild(header);
        card.appendChild(body);
        container.appendChild(card);
    }

    // ── Add a key-value row inside a group ──
    function addAttrRow(wrap, gIdx, key = '', value = '') {
        const rIdx = wrap.querySelectorAll('.attr-group-row').length;

        const row = document.createElement('div');
        row.className = 'attr-group-row';
        row.innerHTML = `
            <input type="text"
                   name="attr_groups[${gIdx}][attrs][${rIdx}][key]"
                   placeholder="e.g. Weight"
                   value="${esc(key)}">
            <input type="text"
                   name="attr_groups[${gIdx}][attrs][${rIdx}][value]"
                   placeholder="e.g. 2kg"
                   value="${esc(value)}">
            <button type="button" class="btn-rm-attr-row" title="Remove row">
                <i class="fas fa-times"></i>
            </button>`;
        row.querySelector('.btn-rm-attr-row').addEventListener('click', () => {
            row.remove();
            reIndexRows(wrap, getGroupIndex(wrap.closest('.attr-group-card')));
        });
        wrap.appendChild(row);
    }

    // ── Helpers ──
    function getGroupIndex(card) {
        return Array.from(container.querySelectorAll('.attr-group-card')).indexOf(card);
    }

    function reIndexGroups() {
        container.querySelectorAll('.attr-group-card').forEach((card, gIdx) => {
            card.querySelector('.group-name-inp').name = `attr_groups[${gIdx}][group]`;
            const wrap = card.querySelector('.attr-rows-wrap');
            reIndexRows(wrap, gIdx);
        });
    }

    function reIndexRows(wrap, gIdx) {
        wrap.querySelectorAll('.attr-group-row').forEach((row, rIdx) => {
            const inputs = row.querySelectorAll('input');
            inputs[0].name = `attr_groups[${gIdx}][attrs][${rIdx}][key]`;
            inputs[1].name = `attr_groups[${gIdx}][attrs][${rIdx}][value]`;
        });
    }

    function esc(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
})();
</script>