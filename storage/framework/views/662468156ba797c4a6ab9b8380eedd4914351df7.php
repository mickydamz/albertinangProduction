

<?php $__env->startSection('content'); ?>
<div class="main-wrap">

    
    <nav style="margin-bottom:20px;font-size:13px;color:var(--ink3);">
        <a href="/" style="color:var(--ink3);">Home</a>
        <span style="margin:0 6px;">›</span>
        <a href="<?php echo e(route('tickets.index')); ?>" style="color:var(--ink3);">Support Tickets</a>
        <span style="margin:0 6px;">›</span>
        <span style="color:var(--ink);">New Ticket</span>
    </nav>

    <div style="max-width:720px;">

        <h1 style="font-family:var(--font-head);font-size:1.4rem;font-weight:700;color:var(--ink);margin-bottom:4px;">
            Open a Support Ticket
        </h1>
        <p style="font-size:13.5px;color:var(--ink3);margin-bottom:24px;">
            Describe your issue and attach screenshots if helpful.
        </p>

        <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;padding:28px 28px 24px;">

            
            <form action="<?php echo e(route('tickets.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12.5px;font-weight:700;color:var(--ink2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px;">
                        Subject <span style="color:#e74c3c;">*</span>
                    </label>
                    <input type="text" name="subject" value="<?php echo e(old('subject')); ?>"
                           placeholder="Brief summary of the issue"
                           style="width:100%;box-sizing:border-box;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--ink);background:var(--surface);outline:none;transition:border-color .2s;font-family:var(--font-body);"
                           onfocus="this.style.borderColor='var(--g400)'"
                           onblur="this.style.borderColor='var(--border)'">
                    <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#e74c3c;font-size:12px;margin-top:5px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12.5px;font-weight:700;color:var(--ink2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px;">
                        Priority <span style="color:#e74c3c;">*</span>
                    </label>
                    <select name="priority"
                            style="width:100%;box-sizing:border-box;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--ink);background:var(--surface);outline:none;font-family:var(--font-body);cursor:pointer;"
                            onfocus="this.style.borderColor='var(--g400)'"
                            onblur="this.style.borderColor='var(--border)'">
                        <option value="low"    <?php echo e(old('priority')=='low'    ? 'selected' : ''); ?>>Low</option>
                        <option value="medium" <?php echo e(old('priority','medium')=='medium' ? 'selected' : ''); ?>>Medium</option>
                        <option value="high"   <?php echo e(old('priority')=='high'   ? 'selected' : ''); ?>>High</option>
                    </select>
                    <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#e74c3c;font-size:12px;margin-top:5px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12.5px;font-weight:700;color:var(--ink2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px;">
                        Description <span style="color:#e74c3c;">*</span>
                    </label>
                    <textarea name="description" rows="6"
                              placeholder="Describe your issue in as much detail as possible…"
                              style="width:100%;box-sizing:border-box;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius);font-size:13.5px;color:var(--ink);background:var(--surface);outline:none;resize:vertical;font-family:var(--font-body);transition:border-color .2s;"
                              onfocus="this.style.borderColor='var(--g400)'"
                              onblur="this.style.borderColor='var(--border)'"><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#e74c3c;font-size:12px;margin-top:5px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12.5px;font-weight:700;color:var(--ink2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px;">
                        Attachments <span style="font-size:11.5px;font-weight:400;color:var(--ink3);text-transform:none;letter-spacing:0;">(optional · up to 5 images · max 5 MB each)</span>
                    </label>

                    
                    <div id="drop-zone"
                         style="border:2px dashed var(--border);border-radius:var(--radius);padding:28px 20px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                         onclick="document.getElementById('img-input').click()"
                         ondragover="event.preventDefault();this.style.borderColor='var(--g400)';this.style.background='var(--g50)'"
                         ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent'"
                         ondrop="handleDrop(event)">
                        <i class="fas fa-cloud-upload-alt" style="font-size:26px;color:var(--g400);margin-bottom:8px;display:block;"></i>
                        <p style="font-size:13.5px;color:var(--ink2);margin:0 0 4px;font-weight:500;">Click to browse or drag & drop images</p>
                        <p style="font-size:12px;color:var(--ink3);margin:0;">JPG, PNG, GIF, WebP</p>
                    </div>

                    <input type="file" id="img-input" name="images[]"
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           multiple style="display:none;"
                           onchange="previewImages(this.files)">

                    <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#e74c3c;font-size:12px;margin-top:5px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color:#e74c3c;font-size:12px;margin-top:5px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    
                    <div id="img-preview"
                         style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:14px;"></div>
                </div>

                
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:var(--g500);color:#fff;border:none;border-radius:var(--radius);font-size:13.5px;font-weight:600;font-family:var(--font-body);cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--g600)'"
                            onmouseout="this.style.background='var(--g500)'">
                        <i class="fas fa-paper-plane"></i> Submit Ticket
                    </button>
                    <a href="<?php echo e(route('tickets.index')); ?>"
                       style="font-size:13.5px;color:var(--ink3);text-decoration:none;">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Shared file list (so both drag-drop and input stay in sync)
let chosenFiles = new DataTransfer();

function previewImages(files) {
    for (const f of files) {
        if (chosenFiles.items.length >= 5) break;
        chosenFiles.items.add(f);
    }
    document.getElementById('img-input').files = chosenFiles.files;
    renderPreviews();
}

function handleDrop(e) {
    e.preventDefault();
    document.getElementById('drop-zone').style.borderColor = 'var(--border)';
    document.getElementById('drop-zone').style.background  = 'transparent';
    previewImages(e.dataTransfer.files);
}

function removeFile(index) {
    const newDT = new DataTransfer();
    const files = chosenFiles.files;
    for (let i = 0; i < files.length; i++) {
        if (i !== index) newDT.items.add(files[i]);
    }
    chosenFiles = newDT;
    document.getElementById('img-input').files = chosenFiles.files;
    renderPreviews();
}

function renderPreviews() {
    const grid = document.getElementById('img-preview');
    grid.innerHTML = '';
    const files = chosenFiles.files;
    for (let i = 0; i < files.length; i++) {
        const url  = URL.createObjectURL(files[i]);
        const wrap = document.createElement('div');
        wrap.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);aspect-ratio:1;background:#f5f5f5;';
        wrap.innerHTML = `
            <img src="${url}" style="width:100%;height:100%;object-fit:cover;display:block;">
            <button type="button" onclick="removeFile(${i})"
                    style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(0,0,0,.55);color:#fff;border:none;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center;line-height:1;">
                <i class="fas fa-times"></i>
            </button>
            <div style="position:absolute;bottom:0;left:0;right:0;padding:4px 6px;background:rgba(0,0,0,.45);font-size:10px;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                ${files[i].name}
            </div>`;
        grid.appendChild(wrap);
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/tickets/create.blade.php ENDPATH**/ ?>