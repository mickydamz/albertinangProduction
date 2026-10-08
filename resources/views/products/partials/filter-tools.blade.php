<style>
.facet-search { width:100%; margin:8px 0 12px; padding:9px 10px; border:1px solid #dfe5dc; border-radius:6px; }
.filter-opt[hidden] { display:none !important; }
.filter-options.facet-searching .filter-opt--overflow { display:flex; }
.filter-group-head:focus-visible { outline:2px solid #4e7a1a; outline-offset:3px; }
.filter-help { font-size:12px; color:#53634c; line-height:1.6; padding:12px; background:#f4f8ef; border-radius:8px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('filterSidebar');
    if (!sidebar) return;
    const body = sidebar.querySelector('.sidebar-body');
    const help = document.createElement('p');
    help.className = 'filter-help';
    help.textContent = 'Choose your filters, then apply them. Select several options in one group to match any; selections across groups narrow the results.';
    body?.prepend(help);
    const type = sidebar.querySelector('#fg-product-type') || sidebar.querySelector('#cust-product-type-opts')?.closest('.filter-group');
    const brands = sidebar.querySelector('#fg-brands') || sidebar.querySelector('#brand-opts')?.closest('.filter-group');
    const price = sidebar.querySelector('#fg-price') || sidebar.querySelector('#price-opts')?.closest('.filter-group');
    if (type && brands) {
        brands.before(type);
        if (price) brands.after(price);
        help.textContent = 'Start with a product type to see relevant specifications. Select several options in a group to match any, then apply your filters.';
    }

    sidebar.querySelectorAll('.filter-group-head').forEach(head => {
        head.setAttribute('role','button'); head.tabIndex = 0;
        head.setAttribute('aria-controls',head.dataset.target);
        const sync = () => head.setAttribute('aria-expanded',String(!head.closest('.filter-group').classList.contains('collapsed')));
        sync(); head.addEventListener('click',sync);
        head.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); head.click(); } });
        const options = document.getElementById(head.dataset.target);
        if (!options || options.querySelectorAll('.filter-opt').length < 6) return;
        const search = document.createElement('input'); search.type = 'search'; search.className = 'facet-search';
        const label = head.textContent.trim(); search.placeholder = 'Find an option'; search.setAttribute('aria-label','Search '+label+' options');
        options.prepend(search);
        search.addEventListener('input', () => {
            const term = search.value.trim().toLowerCase(); options.classList.toggle('facet-searching',!!term);
            options.querySelectorAll('.filter-opt').forEach(row => { row.hidden = !!term && !row.textContent.toLowerCase().includes(term) && !row.querySelector('input')?.checked; });
        });
    });
    const min = document.getElementById('min-price'), max = document.getElementById('max-price');
    min?.setAttribute('aria-label','Minimum price'); max?.setAttribute('aria-label','Maximum price');
    // Validate before the existing apply listener can navigate away.
    document.getElementById('applyFiltersBtn')?.addEventListener('click', e => {
        if (!min || !max) return;
        const invalid = (min.value !== '' && Number(min.value) < 0) || (max.value !== '' && Number(max.value) < 0) || (min.value !== '' && max.value !== '' && Number(max.value) < Number(min.value));
        max.setCustomValidity(invalid ? 'Enter a valid range. Maximum price must be at least the minimum price.' : '');
        if (invalid) { e.preventDefault(); e.stopImmediatePropagation(); max.reportValidity(); }
    }, true);
    [min,max].forEach(input => input?.addEventListener('input',() => max?.setCustomValidity('')));
});
</script>
