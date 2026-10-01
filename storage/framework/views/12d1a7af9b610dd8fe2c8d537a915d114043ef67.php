


<?php $__env->startSection('title', $currency->exists ? 'Edit Currency' : 'Add Currency'); ?>

<?php $__env->startSection('content'); ?>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">

        
        <div class="content-header row">
            <div class="col-12 mb-2">
                <h2 class="content-header-title mb-0">Currencies</h2>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.currencies.index')); ?>">Currencies</a></li>
                    <li class="breadcrumb-item active"><?php echo e($currency->exists ? 'Edit' : 'Add'); ?></li>
                </ol>
            </div>
        </div>


<div class="content-body">
  <div class="row">
    <div class="col-12 col-md-8 col-lg-6">

      
      <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
          <div class="alert-body">
            <ul class="mb-0 ps-1">
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($err); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header border-bottom">
          <h4 class="card-title">
            <?php if($currency->exists): ?>
              <span class="badge bg-label-warning me-1"><?php echo e($currency->code); ?></span>
              Edit Currency
            <?php else: ?>
              <i class="fas fa-circle-plus me-50 font-small-4 text-primary"></i>
              Add New Currency
            <?php endif; ?>
          </h4>
        </div>

        <div class="card-body pt-2">
          <form method="POST"
                action="<?php echo e($currency->exists
                           ? route('admin.currencies.update', $currency)
                           : route('admin.currencies.store')); ?>"
                class="mt-1">
            <?php echo csrf_field(); ?>
            <?php if($currency->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            
            <div class="mb-1">
              <label class="form-label" for="code">
                Currency Code
                <span class="text-danger">*</span>
              </label>
              <?php if(!$currency->exists): ?>
                <input type="text"
                       id="code"
                       name="code"
                       class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('code')); ?>"
                       placeholder="e.g. USD, GBP, EUR"
                       maxlength="10"
                       style="text-transform:uppercase;"
                       required>
                <div class="form-text">3-letter ISO code. Will be saved in uppercase.</div>
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              <?php else: ?>
                <input type="text"
                       id="code"
                       class="form-control"
                       value="<?php echo e($currency->code); ?>"
                       disabled>
                <div class="form-text text-muted">Currency code cannot be changed after creation.</div>
              <?php endif; ?>
            </div>

            
            <div class="mb-1">
              <label class="form-label" for="symbol">
                Symbol
                <span class="text-danger">*</span>
              </label>
              <input type="text"
                     id="symbol"
                     name="symbol"
                     class="form-control <?php $__errorArgs = ['symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                     value="<?php echo e(old('symbol', $currency->symbol ?? '')); ?>"
                     placeholder="e.g. $, £, €"
                     maxlength="10"
                     required>
              <?php $__errorArgs = ['symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="mb-1">
              <label class="form-label" for="name">
                Full Name
                <span class="text-danger">*</span>
              </label>
              <input type="text"
                     id="name"
                     name="name"
                     class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                     value="<?php echo e(old('name', $currency->name ?? '')); ?>"
                     placeholder="e.g. US Dollar"
                     maxlength="80"
                     required>
              <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="mb-1">
              <label class="form-label" for="rate_to_ngn">
                Rate to NGN
                <span class="text-danger">*</span>
              </label>
              <div class="input-group">
                <span class="input-group-text">₦ 1 <?php echo e($currency->exists ? $currency->code : 'UNIT'); ?> =</span>
                <input type="number"
                       id="rate_to_ngn"
                       name="rate_to_ngn"
                       step="0.000001"
                       min="0.000001"
                       class="form-control <?php $__errorArgs = ['rate_to_ngn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       value="<?php echo e(old('rate_to_ngn', $currency->rate_to_ngn ?? '')); ?>"
                       placeholder="1500.000000"
                       <?php echo e(($currency->is_base ?? false) ? 'disabled' : ''); ?>

                       required>
                <span class="input-group-text">NGN</span>
                <?php $__errorArgs = ['rate_to_ngn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              <?php if($currency->is_base ?? false): ?>
                <div class="form-text text-warning">
                  <i class="fas fa-lock font-small-3 me-25"></i>
                  Base currency rate is always 1 and cannot be changed.
                </div>
              <?php else: ?>
                <div class="form-text">How many Naira equals 1 unit of this currency. E.g. enter <strong>1500</strong> if $1 = ₦1,500.</div>
              <?php endif; ?>
            </div>

            
            <div class="mb-1">
              <label class="form-label" for="sort_order">Sort Order</label>
              <input type="number"
                     id="sort_order"
                     name="sort_order"
                     min="0"
                     class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                     value="<?php echo e(old('sort_order', $currency->sort_order ?? 99)); ?>">
              <div class="form-text">Lower numbers appear first in the currency list.</div>
              <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="mb-2">
              <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox"
                       class="form-check-input"
                       name="is_active"
                       id="is_active"
                       value="1"
                       <?php echo e(old('is_active', $currency->is_active ?? true) ? 'checked' : ''); ?>>
                <label class="form-check-label" for="is_active">
                  Active
                  <span class="text-muted font-small-2 ms-25">(visible to shoppers)</span>
                </label>
              </div>
            </div>

            
            <div class="d-flex gap-1 mt-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas <?php echo e($currency->exists ? 'fa-save' : 'fa-plus'); ?> me-50 font-small-4"></i>
                <?php echo e($currency->exists ? 'Save Changes' : 'Add Currency'); ?>

              </button>
              <a href="<?php echo e(route('admin.currencies.index')); ?>" class="btn btn-outline-secondary">
                Cancel
              </a>
            </div>

          </form>
        </div>
      </div>

    </div>

    
    <div class="col-12 col-md-4 col-lg-6 d-none d-md-block">
      <div class="card bg-light-primary border-0">
        <div class="card-body">
          <h5 class="card-title text-primary mb-1">
            <i class="fas fa-circle-info me-50 font-small-4"></i>
            Currency Tips
          </h5>
          <ul class="ps-1 mb-0" style="font-size:13.5px;line-height:1.8;">
            <li>Use the standard <strong>3-letter ISO 4217</strong> code (e.g. USD, GBP, EUR).</li>
            <li>The <strong>rate to NGN</strong> is used to convert product prices when shoppers switch currency.</li>
            <li>Keep rates updated regularly to reflect current exchange rates.</li>
            <li>Only <strong>active</strong> currencies appear in the storefront switcher.</li>
            <li>The <strong>base currency (NGN)</strong> rate is always locked at 1.</li>
          </ul>
        </div>
      </div>

      <?php if($currency->exists): ?>
      <div class="card mt-1">
        <div class="card-body">
          <h6 class="fw-bold mb-1">Current Values</h6>
          <table class="table table-sm table-borderless mb-0" style="font-size:13.5px;">
            <tr>
              <td class="text-muted ps-0">Code</td>
              <td><span class="badge bg-label-secondary"><?php echo e($currency->code); ?></span></td>
            </tr>
            <tr>
              <td class="text-muted ps-0">Symbol</td>
              <td><strong><?php echo e($currency->symbol); ?></strong></td>
            </tr>
            <tr>
              <td class="text-muted ps-0">Rate</td>
              <td>₦<?php echo e(number_format($currency->rate_to_ngn, 2)); ?></td>
            </tr>
            <tr>
              <td class="text-muted ps-0">Status</td>
              <td>
                <?php if($currency->is_active): ?>
                  <span class="badge bg-label-success">Active</span>
                <?php else: ?>
                  <span class="badge bg-label-danger">Inactive</span>
                <?php endif; ?>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>
</div>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  // Auto-uppercase the currency code as the user types
  const codeInput = document.getElementById('code');
  if (codeInput) {
    codeInput.addEventListener('input', function () {
      const pos = this.selectionStart;
      this.value = this.value.toUpperCase();
      this.setSelectionRange(pos, pos);
    });
  }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/currencies/form.blade.php ENDPATH**/ ?>