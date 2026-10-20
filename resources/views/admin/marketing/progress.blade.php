<script>
(function () {
    const initialToken = @json($progressToken);
    const endpoint = new URL(window.location.href);
    endpoint.searchParams.set('progress', '1');
    let editing = false;
    let checking = false;
    // Preserve unsaved draft content and recipient selections during background refreshes.
    document.addEventListener('input', function (event) {
        if (event.target.closest('form, [contenteditable]')) editing = true;
    });
    document.addEventListener('change', function (event) {
        if (event.target.closest('form')) editing = true;
    });
    async function checkProgress() {
        if (document.hidden || editing || checking) return;
        checking = true;
        try {
            const response = await fetch(endpoint, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) return;
            const progress = await response.json();
            if (progress.token && progress.token !== initialToken && !editing) window.location.reload();
        } catch (_) { /* Keep the last confirmed progress during connection failures. */ }
        finally { checking = false; }
    }
    setInterval(checkProgress, 10000);
    document.addEventListener('visibilitychange', checkProgress);
    window.addEventListener('pageshow', checkProgress);
})();
</script>
