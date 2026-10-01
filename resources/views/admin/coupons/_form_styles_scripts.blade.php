<style>
.coupon-code-pill {
    display: inline-block;
    background: #f0f4ff;
    color: #3451b2;
    border: 1px dashed #a5b4fc;
    border-radius: 5px;
    padding: 3px 10px;
    font-family: monospace;
    font-size: 0.88rem;
    font-weight: 700;
    letter-spacing: 0.08em;
}

.discount-type-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6c757d;
    transition: all 0.15s;
    flex: 1;
    text-align: center;
}
.discount-type-card input[type="radio"] { display: none; }
.discount-type-card:hover { border-color: #28c76f; color: #28c76f; }
.discount-type-card.selected {
    border-color: #28c76f;
    background: #f0fdf4;
    color: #28c76f;
}

.coupon-preview {
    background: #f8f9fa;
    border: 1px dashed #d0d5dd;
    border-radius: 8px;
    padding: 0.75rem 1rem;
}
.coupon-preview__label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #6e84a3;
    font-weight: 600;
    margin-bottom: 6px;
}
.coupon-preview__body { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.coupon-preview__desc { font-size: 0.9rem; color: #2c3e50; font-weight: 600; }

.mb-1 { margin-bottom: 1rem !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof feather !== 'undefined') feather.replace();

    // Sync card selected state on load — scoped per radio group so multiple
    // card groups (discount type, usage per customer) don't clobber each other.
    document.querySelectorAll('.discount-type-card input[type="radio"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const group = this.name;
            document.querySelectorAll('.discount-type-card input[type="radio"][name="' + group + '"]')
                .forEach(r => r.closest('.discount-type-card').classList.remove('selected'));
            this.closest('.discount-type-card').classList.add('selected');
        });
    });

    // Live preview
    const codeInput = document.querySelector('input[name="code"]');
    const valueInput = document.querySelector('input[name="value"]');
    if (codeInput) codeInput.addEventListener('input', updatePreview);
    if (valueInput) valueInput.addEventListener('input', updatePreview);

    updatePreview();
    updateDiscountType();

    @if(isset($mode) && $mode === 'create')
    // Random code generator
    document.getElementById('generateCodeBtn')?.addEventListener('click', function () {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let code = '';
        for (let i = 0; i < 8; i++) code += chars[Math.floor(Math.random() * chars.length)];
        document.querySelector('input[name="code"]').value = code;
        updatePreview();
    });
    @endif
});

function updateUsageType() {
    document.querySelectorAll('input[name="multi_use"]').forEach(function (radio) {
        radio.closest('.discount-type-card').classList.toggle('selected', radio.checked);
    });
}

function updateDiscountType() {
    const isFixed = document.getElementById('type_fixed')?.checked;
    const prefix = document.getElementById('valuePrefix');
    const maxDiscountRow = document.getElementById('maxDiscountRow');

    if (prefix) prefix.textContent = isFixed ? '₦' : '%';
    if (maxDiscountRow) maxDiscountRow.style.display = isFixed ? 'none' : '';

    document.querySelectorAll('.discount-type-card').forEach(function (card) {
        card.classList.toggle('selected', card.querySelector('input[type="radio"]')?.checked);
    });

    updatePreview();
}

function updatePreview() {
    const code  = (document.querySelector('input[name="code"]')?.value || '').trim().toUpperCase() || '—';
    const value = parseFloat(document.querySelector('input[name="value"]')?.value) || 0;
    const isFixed = document.getElementById('type_fixed')?.checked;
    const maxAmt  = parseFloat(document.querySelector('input[name="max_discount_amount"]')?.value) || 0;

    document.getElementById('previewCode').textContent = code || '—';

    let desc = '—';
    if (value > 0) {
        if (isFixed) {
            desc = '₦' + value.toLocaleString() + ' off';
        } else {
            desc = value + '% off';
            if (maxAmt > 0) desc += ' (max ₦' + maxAmt.toLocaleString() + ')';
        }
    }
    document.getElementById('previewDesc').textContent = desc;
}
</script>