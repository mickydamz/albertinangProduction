{{--
    Refund confirmation guard.

    Intercepts any <form data-refund-guard> and, when the chosen action will
    actually issue a refund, shows a SweetAlert "are you sure" that spells out
    exactly what happens (amount, gateway, email, timing). Only submits on confirm.

    Form data-* contract:
      data-refund-guard                 marker (required)
      data-refund-statuses="a,b"        status values (in select[name=status]) that trigger a refund.
                                         Omit for a form that always refunds (e.g. the refund form).
      data-amount="50000"               fixed refund amount, OR
      data-amount-selector="#id"        live amount read from an input
      data-currency="₦"                 display currency symbol (default ₦)
      data-gateway="paystack"           order payment method
      data-email="buyer@x.com"          customer email (for the message)
      data-already-refunded="1"         if set, warns it's a no-op instead of a charge
--}}
@once
@push('scripts')
<script src="{{ asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js') }}"></script>
<script>
(function () {
    function money(amount, currency) {
        if (amount === undefined || amount === null || amount === '' || isNaN(Number(amount))) return 'the order amount';
        return (currency || '₦') + Number(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.addEventListener('submit', function (e) {
        var form = e.target.closest('form[data-refund-guard]');
        if (!form || form.dataset.confirmed === '1') return;

        // Does THIS submission actually issue a refund?
        var statuses = (form.dataset.refundStatuses || '').split(',').map(function (s) { return s.trim(); }).filter(Boolean);
        var willRefund;
        if (statuses.length) {
            var sel = form.querySelector('[name="status"]');
            willRefund = sel ? statuses.indexOf(sel.value) !== -1 : false;
        } else {
            willRefund = true; // always-refund form
        }
        if (!willRefund) return; // e.g. "reject" / "approve-only" — submit normally

        e.preventDefault();

        var gateway = (form.dataset.gateway || '').toLowerCase();
        var online = gateway === 'paystack' || gateway === 'stripe';
        var alreadyRefunded = form.dataset.alreadyRefunded === '1';
        var email = form.dataset.email || 'the customer';
        var amount = form.dataset.amount;
        if (form.dataset.amountSelector) {
            var inp = form.querySelector(form.dataset.amountSelector);
            if (inp) amount = inp.value;
        }
        var amtText = money(amount, form.dataset.currency);

        var opts;
        if (alreadyRefunded) {
            opts = {
                icon: 'info',
                title: 'Already refunded',
                html: 'This order already has a refund on record. Saving will <b>not</b> charge the customer again — it only records your decision.',
                confirmButtonText: 'Save decision',
                confirmButtonColor: '#7367f0'
            };
        } else if (!online) {
            opts = {
                icon: 'warning',
                title: 'Manual refund required',
                html: 'This order was paid by <b>' + (gateway || 'an offline method') + '</b>, so no automatic refund can be sent.<br>You will need to refund <b>' + email + '</b> manually. Continue recording the decision?',
                confirmButtonText: 'Yes, continue',
                confirmButtonColor: '#ff9f43'
            };
        } else {
            opts = {
                icon: 'warning',
                title: 'Issue a refund?',
                html: '<div style="text-align:left">This will <b>refund ' + amtText + '</b> to <b>' + email + '</b> via <b>' + gateway +
                      '</b>, back to their original payment method.' +
                      '<ul style="margin:.5rem 0 0 1.1rem;padding:0;line-height:1.5">' +
                      '<li>The customer will be <b>emailed</b> about the refund.</li>' +
                      '<li>Funds usually arrive in <b>5–10 business days</b>.</li>' +
                      '<li>This <b>cannot be undone</b> from here.</li>' +
                      '</ul></div>',
                confirmButtonText: 'Yes, refund the customer',
                confirmButtonColor: '#ea5455'
            };
        }

        Swal.fire(Object.assign({
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true
        }, opts)).then(function (res) {
            if (res.isConfirmed) {
                form.dataset.confirmed = '1';
                form.submit();
            }
        });
    }, true);
})();
</script>
@endpush
@endonce
