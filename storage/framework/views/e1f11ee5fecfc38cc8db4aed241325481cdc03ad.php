
<?php
    $hdrStoreName    = $storeName    ?? ($appStoreName ?? 'Albertina Nigeria');
    $hdrStoreAddr    = $storeAddr     ?? ($storeAddress ?? '');
    $hdrStoreContact = $storeContact ?? ($storeEmail ?? '');
    $hdrStorePhone   = $storePhone   ?? null;
    $hdrNote         = $invHeaderNote ?? '';
    $hdrIssueDate    = $issueDate     ?? now()->format('d F Y');
    $hdrLogo         = !empty($appStoreLogo) ? asset('storage/' . $appStoreLogo) : asset('image.png');
?>

<table class="inv-header-tbl">
    <tr>
        <td style="width:55%;">
            <img src="<?php echo e($hdrLogo); ?>" alt="<?php echo e($hdrStoreName); ?>" class="inv-brand-logo">
        </td>
        <td style="width:45%;" class="inv-right-head">
            <span class="inv-title">INVOICE</span>
            <div class="inv-contact-small">
                <?php echo e($hdrStoreName); ?><?php if($hdrStoreAddr): ?> | <?php echo e($hdrStoreAddr); ?><?php endif; ?><br>
                <?php echo e($hdrStoreContact); ?><?php if($hdrStorePhone): ?> &nbsp;|&nbsp; <?php echo e($hdrStorePhone); ?><?php endif; ?>
                <?php if(!empty($hdrNote)): ?><br><em><?php echo e($hdrNote); ?></em><?php endif; ?>
            </div>
        </td>
    </tr>
</table>

<div class="inv-issue-date">Issue Date: <?php echo e($hdrIssueDate); ?></div>

<div class="inv-green-bar"></div>
<?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/partials/invoice-header.blade.php ENDPATH**/ ?>