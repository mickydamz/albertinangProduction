# Failure evidence

Run IntelliJ configuration **06 - Full payment and lifecycle tests**.

After a run, open `reports/FAILURES.md` for the exact failing action, source file and line, and evidence links. Open configuration **04 - View test report** for the interactive report. Expand the failed test to view screenshots, the timeline and trace.

Each failure records the customer and admin page addresses, gateway frame text, named journey steps, failed requests, HTTP error statuses and browser errors. The trace shows the action immediately before failure and its locator. Password fields are masked in additional screenshots; text evidence redacts credentials and email addresses. Traces and videos may contain dummy account information: keep them private.

Configuration 06 archives previous results under `reports/archive/` before starting. A failure in payment prevents that scenario from reaching cancellation, return or review verification; those downstream features must not be reported as verified.

The exact failing locator and source location are also included when the error occurs outside a named journey step. Paystack initialization errors include the gateway message and response status; return decisions check that Save Decision actually sends a server request. A browser session interrupted by computer sleep can invalidate payment waits; rerun that affected scenario before treating it as a website defect.
