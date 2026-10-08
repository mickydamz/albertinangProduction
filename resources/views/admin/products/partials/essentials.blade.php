<style>
#pane-basic > section { border-top:1px solid #e5e7eb; margin-top:24px; padding-top:24px; }
.product-guide { padding:16px; background:#f3f8ee; border-radius:8px; margin-bottom:20px; color:#34472c; }
.product-guide p { margin:4px 0 0; }
.pe-nav-item.active { background:#edf5e5; color:#3d641b; }
.pe-panel-ico { background:#edf5e5; color:#3d641b; }
</style>
<script>
// Consolidate the essentials before the existing editor initializes its section navigation.
(function () {
    const basic = document.getElementById('pane-basic');
    if (!basic) return;
    const heading = basic.querySelector('h4');
    if (heading) heading.textContent = 'Product essentials';
    const nav = document.querySelector('[data-target="pane-basic"]');
    if (nav) nav.textContent = '1. Product essentials';
    const guide = document.createElement('div');
    guide.className = 'product-guide';
    guide.innerHTML = '<strong>Add a product in one place</strong><p>Enter the name, category, price and stock, add photos, then choose whether to publish. Choose a subcategory before publishing when one is available: this controls the customer product-type filter. Use specification names such as Screen Size or Load Capacity and include units. Product type is managed by the category and subcategory. Variants, specifications and installation are optional.</p>';
    basic.prepend(guide);
    ['pane-org','pane-pricing','pane-images','pane-visibility'].forEach(id => {
        const panel = document.getElementById(id);
        if (!panel) return;
        panel.classList.remove('pe-panel','active');
        const section = document.createElement('section');
        section.append(panel);
        basic.append(section);
        document.querySelector('[data-target="'+id+'"]')?.remove();
    });
    ['pane-variants','pane-specs','pane-install'].forEach((id,index) => {
        const button = document.querySelector('[data-target="'+id+'"]');
        if (button) button.append(' (optional)');
    });
    const form = document.getElementById('product-form');
    form?.addEventListener('invalid', event => {
        const panel = event.target.closest('.pe-panel');
        if (panel && !panel.classList.contains('active')) document.querySelector('[data-target="'+panel.id+'"]')?.click();
    }, true);
})();
</script>
