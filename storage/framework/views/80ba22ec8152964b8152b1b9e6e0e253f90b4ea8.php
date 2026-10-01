<?php $__env->startSection('content'); ?>
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">FAQs</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Home</a></li>
                            <li class="breadcrumb-item active">FAQs</li>
                        </ol>
                    </div>
                    <a href="<?php echo e(route('admin.faqs.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-50"></i> Add FAQ
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-question-circle me-50 text-primary"></i>
                        All FAQs <span class="badge bg-secondary ms-1"><?php echo e($faqs->total()); ?></span>
                    </h4>
                    <?php echo $__env->make('admin.partials.search-box', ['action' => route('admin.faqs.index'), 'value' => $search, 'placeholder' => 'Search questions, answers…'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="card-body p-0">
                    <?php if($faqs->isEmpty()): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-question-circle fa-2x mb-2 d-block"></i>
                            No FAQs yet. <a href="<?php echo e(route('admin.faqs.create')); ?>">Add the first one.</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="faqsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Question</th>
                                        <th style="width:130px;">Category</th>
                                        <th style="width:80px;">Order</th>
                                        <th style="width:90px;">Status</th>
                                        <th style="width:110px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td data-label="#" class="text-muted"><?php echo e($faq->id); ?></td>
                                        <td data-label="Question">
                                            <div class="fw-semibold" style="font-size:13px;"><?php echo e(Str::limit($faq->question, 90)); ?></div>
                                            <div class="text-muted" style="font-size:12px;"><?php echo e(Str::limit(strip_tags($faq->answer), 80)); ?></div>
                                        </td>
                                        <td data-label="Category">
                                            <span class="badge bg-light text-dark border" style="font-size:11px;"><?php echo e(ucfirst($faq->category)); ?></span>
                                        </td>
                                        <td data-label="Order" class="text-muted" style="font-size:13px;"><?php echo e($faq->sort_order); ?></td>
                                        <td data-label="Status">
                                            <?php if($faq->is_active): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Hidden</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Actions">
                                            <a href="<?php echo e(route('admin.faqs.edit', $faq)); ?>"
                                               class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                <i class="fas fa-pencil"></i>
                                            </a>
                                            <form method="POST" action="<?php echo e(route('admin.faqs.destroy', $faq)); ?>"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this FAQ?')">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if($faqs->hasPages()): ?>
                    <div class="card-footer d-flex justify-content-center">
                        <?php echo e($faqs->links()); ?>

                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
  /* Responsive table — stack rows into cards on mobile (matches Products) */
  @media (max-width: 768px) {
    #faqsTable thead { display: none; }
    #faqsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #faqsTable td {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
      width: auto !important;
    }
    #faqsTable td:last-child { border-bottom: none; }
    #faqsTable td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
    #faqsTable td > :not(.badge) { text-align: right; }
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminlayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/admin/faqs/index.blade.php ENDPATH**/ ?>