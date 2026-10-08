{{--
    Shared invoice header — logo · INVOICE title · store contact · issue date · accent bar.
    Included by BOTH the real invoice (sims/invoice.blade.php) and the admin invoice
    template editor, so the header is guaranteed to look identical in both places.

    Expected (all optional — sensible fallbacks provided):
      $appStoreName / $appStoreLogo, $storeAddress, $storeEmail, $storePhone,
      $invHeaderNote, $issueDate (string)
--}}
@php
    $hdrStoreName    = $storeName    ?? ($appStoreName ?? 'AlbertinaNG');
    $hdrStoreAddr    = $storeAddr     ?? ($storeAddress ?? '');
    $hdrStoreContact = $storeContact ?? ($storeEmail ?? '');
    $hdrStorePhone   = $storePhone   ?? null;
    $hdrNote         = $invHeaderNote ?? '';
    $hdrIssueDate    = $issueDate     ?? now()->format('d F Y');
    $hdrLogo         = !empty($appStoreLogo) ? asset('storage/' . $appStoreLogo) : asset('image.png');
@endphp

<table class="inv-header-tbl">
    <tr>
        <td style="width:55%;">
            <img src="{{ $hdrLogo }}" alt="{{ $hdrStoreName }}" class="inv-brand-logo">
        </td>
        <td style="width:45%;" class="inv-right-head">
            <span class="inv-title">INVOICE</span>
            <div class="inv-contact-small">
                {{ $hdrStoreName }}@if($hdrStoreAddr) | {{ $hdrStoreAddr }}@endif<br>
                {{ $hdrStoreContact }}@if($hdrStorePhone) &nbsp;|&nbsp; {{ $hdrStorePhone }}@endif
                @if(!empty($hdrNote))<br><em>{{ $hdrNote }}</em>@endif
            </div>
        </td>
    </tr>
</table>

<div class="inv-issue-date">Issue Date: {{ $hdrIssueDate }}</div>

<div class="inv-green-bar"></div>
