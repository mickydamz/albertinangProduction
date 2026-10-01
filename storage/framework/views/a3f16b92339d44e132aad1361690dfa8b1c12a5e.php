
<form method="GET" action="<?php echo e($action); ?>"
      class="d-flex flex-wrap gap-1 ms-md-auto"
      style="flex:1 1 300px; max-width:520px; min-width:0;">
    <input type="search" name="search" value="<?php echo e($value ?? ''); ?>"
           class="form-control form-control-sm"
           style="flex:1 1 150px; min-width:0;"
           placeholder="<?php echo e($placeholder ?? 'Search…'); ?>">
    <button type="submit" class="btn btn-sm btn-primary text-nowrap" style="min-width:92px;">
        <i class="fas fa-search me-1"></i> Search
    </button>
    <?php if(!empty($value)): ?>
        <a href="<?php echo e($action); ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
    <?php endif; ?>
</form>
<?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/partials/search-box.blade.php ENDPATH**/ ?>