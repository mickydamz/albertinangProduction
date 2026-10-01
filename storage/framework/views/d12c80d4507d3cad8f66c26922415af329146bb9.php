

<?php $__env->startSection('content'); ?>
<div class="main-wrap">

    
    <nav style="margin-bottom:20px;font-size:13px;color:var(--ink3);">
        <a href="/" style="color:var(--ink3);">Home</a>
        <span style="margin:0 6px;">›</span>
        <span style="color:var(--ink);">Support Tickets</span>
    </nav>

    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-family:var(--font-head);font-size:1.5rem;font-weight:700;color:var(--ink);margin-bottom:4px;">
                My Support Tickets
            </h1>
            <p style="font-size:13.5px;color:var(--ink3);">
                Track and manage your support requests.
            </p>
        </div>
        <a href="<?php echo e(route('tickets.create')); ?>"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:var(--g500);color:#fff;border-radius:var(--radius);font-size:13.5px;font-weight:600;font-family:var(--font-body);text-decoration:none;transition:background .2s;"
           onmouseover="this.style.background='var(--g600)'"
           onmouseout="this.style.background='var(--g500)'">
            <i class="fas fa-plus"></i> New Ticket
        </a>
    </div>

    
    <?php if(session('success')): ?>
        <div style="background:#eafaf1;border:1px solid #a9dfbf;border-radius:var(--radius);padding:13px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#1e8449;">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('status')): ?>
        <div style="background:#eaf4fb;border:1px solid #aed6f1;border-radius:var(--radius);padding:13px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13.5px;color:#2980b9;">
            <i class="fas fa-info-circle"></i> <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    
    <?php
        $allTickets = $tickets->getCollection();
        $total   = $tickets->total();
        $open    = $allTickets->where('status','open')->count();
        $pending = $allTickets->where('status','pending')->count();
        $closed  = $allTickets->where('status','closed')->count();
    ?>

    <div class="tickets-stats-grid"
         style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">

        <?php $__currentLoopData = [
            ['Total',   $total,   'fa-ticket-alt',     'var(--g500)', 'var(--g50)',  'var(--border2)'],
            ['Open',    $open,    'fa-hourglass-half', '#2980b9',     '#eaf4fb',    '#aed6f1'],
            ['Pending', $pending, 'fa-spinner',        '#e67e22',     '#fef9e7',    '#fad7a0'],
            ['Closed',  $closed,  'fa-check-circle',   '#27ae60',     '#eafaf1',    '#a9dfbf'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;padding:18px 16px;display:flex;align-items:center;gap:14px;">
                <div style="width:40px;height:40px;border-radius:10px;background:<?php echo e($stat[4]); ?>;border:1px solid <?php echo e($stat[5]); ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas <?php echo e($stat[2]); ?>" style="color:<?php echo e($stat[3]); ?>;font-size:16px;"></i>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;font-family:var(--font-head);color:var(--ink);line-height:1;"><?php echo e($stat[1]); ?></div>
                    <div style="font-size:12px;color:var(--ink3);margin-top:2px;"><?php echo e($stat[0]); ?></div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>

    
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:0;overflow:hidden;">

        <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <span style="font-size:14px;font-weight:600;color:var(--ink);font-family:var(--font-head);display:flex;align-items:center;gap:8px;">
                <i class="fas fa-list" style="color:var(--g500);font-size:13px;"></i> All Tickets
            </span>
            <span style="font-size:12.5px;color:var(--ink3);"><?php echo e($tickets->total()); ?> ticket(s) total</span>
        </div>

        <?php if($tickets->isEmpty()): ?>
            <div style="padding:60px 20px;text-align:center;">
                <div style="width:64px;height:64px;border-radius:16px;background:var(--g50);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fas fa-ticket-alt" style="font-size:26px;color:var(--g400);"></i>
                </div>
                <div style="font-size:15px;font-weight:600;color:var(--ink);margin-bottom:6px;">No tickets yet</div>
                <p style="font-size:13.5px;color:var(--ink3);margin-bottom:20px;">Open a ticket and our support team will help you out.</p>
                <a href="<?php echo e(route('tickets.create')); ?>"
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:var(--g500);color:#fff;border-radius:var(--radius);font-size:13.5px;font-weight:600;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='var(--g600)'"
                   onmouseout="this.style.background='var(--g500)'">
                    <i class="fas fa-plus"></i> Create your first ticket
                </a>
            </div>

        <?php else: ?>

            <div style="overflow-x:auto;" class="tickets-table-wrap">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:var(--surf2);border-bottom:1px solid var(--border);">
                            <?php $__currentLoopData = ['Subject','Priority','Status','Last Updated','Action']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th style="padding:11px 18px;text-align:left;font-size:11.5px;font-weight:700;color:var(--ink3);letter-spacing:.6px;text-transform:uppercase;white-space:nowrap;">
                                    <?php echo e($h); ?>

                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                      <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $lastReply  = $ticket->replies->last();
                            $adminReply = ($lastReply && isset($lastReply->user) && $lastReply->user->role === 'admin')
                                          ? $lastReply : null;

                            $pMap = [
                                'low'    => ['Low',    '#27ae60','#eafaf1','#a9dfbf','fa-arrow-down'],
                                'medium' => ['Medium', '#e67e22','#fef9e7','#fad7a0','fa-ellipsis-h'],
                                'high'   => ['High',   '#c0392b','#fff5f5','#f5c6c6','fa-exclamation-triangle'],
                            ];
                            $sMap = [
                                'open'    => ['Open',    '#2980b9','#eaf4fb','#aed6f1','fa-hourglass-half'],
                                'pending' => ['Pending', '#e67e22','#fef9e7','#fad7a0','fa-spinner'],
                                'closed'  => ['Closed',  '#27ae60','#eafaf1','#a9dfbf','fa-check-circle'],
                            ];
                            $p = $pMap[$ticket->priority] ?? ['Unknown','#888','#f5f5f5','#ddd','fa-question'];
                            $s = $sMap[$ticket->status]   ?? ['Unknown','#888','#f5f5f5','#ddd','fa-question'];

                            // Show max 3 thumbnails in the index list
                            $thumbs = array_slice($ticket->images ?? [], 0, 3);
                            $extraCount = max(0, count($ticket->images ?? []) - 3);
                        ?>

                        <tr style="border-bottom:1px solid var(--border);transition:background .15s;"
                            onmouseover="this.style.background='var(--surf2)'"
                            onmouseout="this.style.background='transparent'">

                            
                            <td data-label="Subject" style="padding:14px 18px;max-width:280px;">
                                <a href="<?php echo e(route('tickets.show', $ticket->id)); ?>"
                                   style="font-size:13.5px;font-weight:500;color:var(--ink);text-decoration:none;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:color .15s;"
                                   onmouseover="this.style.color='var(--g600)'"
                                   onmouseout="this.style.color='var(--ink)'">
                                    <?php echo e(\Illuminate\Support\Str::limit($ticket->subject, 55)); ?>

                                </a>

                                
                                <div style="display:flex;align-items:center;gap:8px;margin-top:4px;flex-wrap:wrap;">
                                    <?php if($adminReply): ?>
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10.5px;font-weight:600;background:#eaf4fb;border:1px solid #aed6f1;color:#2980b9;">
                                            <i class="fas fa-reply" style="font-size:9px;"></i> Support replied
                                        </span>
                                        <span style="font-size:11px;color:var(--ink3);"><?php echo e($adminReply->created_at->diffForHumans()); ?></span>
                                    <?php else: ?>
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:10.5px;font-weight:600;background:var(--surf2);border:1px solid var(--border);color:var(--ink3);">
                                            <i class="fas fa-clock" style="font-size:9px;"></i> Awaiting reply
                                        </span>
                                    <?php endif; ?>
                                </div>

                                
                                <?php if(!empty($thumbs)): ?>
                                    <div style="display:flex;align-items:center;gap:5px;margin-top:8px;flex-wrap:wrap;">
                                        <?php $__currentLoopData = $thumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('tickets.show', $ticket->id)); ?>"
                                               style="display:block;width:40px;height:40px;border-radius:6px;overflow:hidden;border:1px solid var(--border);flex-shrink:0;">
                                                <img src="<?php echo e(Storage::url($img)); ?>"
                                                     alt="Attachment"
                                                     style="width:100%;height:100%;object-fit:cover;display:block;">
                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($extraCount > 0): ?>
                                            <span style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:6px;border:1px solid var(--border);background:var(--surf2);font-size:11px;font-weight:700;color:var(--ink3);">
                                                +<?php echo e($extraCount); ?>

                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            
                            <td data-label="Priority" style="padding:14px 18px;white-space:nowrap;">
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:<?php echo e($p[2]); ?>;border:1px solid <?php echo e($p[3]); ?>;color:<?php echo e($p[1]); ?>;">
                                    <i class="fas <?php echo e($p[4]); ?>" style="font-size:10px;"></i> <?php echo e($p[0]); ?>

                                </span>
                            </td>

                            
                            <td data-label="Status" style="padding:14px 18px;white-space:nowrap;">
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11.5px;font-weight:600;background:<?php echo e($s[2]); ?>;border:1px solid <?php echo e($s[3]); ?>;color:<?php echo e($s[1]); ?>;">
                                    <i class="fas <?php echo e($s[4]); ?>" style="font-size:10px;"></i> <?php echo e($s[0]); ?>

                                </span>
                            </td>

                            
                            <td data-label="Last Updated" style="padding:14px 18px;font-size:13px;color:var(--ink3);white-space:nowrap;">
                                <i class="fas fa-clock" style="margin-right:5px;font-size:11px;"></i>
                                <?php echo e($ticket->updated_at->diffForHumans()); ?>

                            </td>

                            
                            <td data-label="Action" style="padding:14px 18px;white-space:nowrap;">
                                <a href="<?php echo e(route('tickets.show', $ticket->id)); ?>"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--border);border-radius:var(--radius);font-size:12.5px;color:var(--ink2);text-decoration:none;transition:all .2s;"
                                   onmouseover="this.style.borderColor='var(--g400)';this.style.color='var(--g600)';this.style.background='var(--g50)'"
                                   onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--ink2)';this.style.background='transparent'">
                                    <i class="fas fa-eye" style="font-size:11px;"></i> View
                                </a>
                            </td>

                        </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($tickets->hasPages()): ?>
                <div style="padding:16px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
                    <?php echo e($tickets->links()); ?>

                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

</div>

<?php $__env->startPush('styles'); ?>
<style>
@media (max-width: 768px) {

    .tickets-stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }

    .tickets-table-wrap table thead {
        display: none;
    }

    .tickets-table-wrap table tbody tr {
        display: block;
        margin: 0 0 14px;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: var(--surface);
        box-shadow: var(--shadow-sm);
    }

    .tickets-table-wrap table tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 11px 14px !important;
        border-bottom: 1px solid var(--border) !important;
        font-size: 13px;
        max-width: 100% !important;
        white-space: normal !important;
        gap: 10px;
    }

    .tickets-table-wrap table tbody td:last-child {
        border-bottom: none !important;
        justify-content: flex-end;
    }

    .tickets-table-wrap table tbody td::before {
        content: attr(data-label);
        font-size: 11px;
        font-weight: 700;
        color: var(--ink3);
        text-transform: uppercase;
        letter-spacing: .5px;
        flex-shrink: 0;
        min-width: 90px;
    }

    .tickets-table-wrap table tbody td[data-label="Subject"] {
        flex-direction: column;
        align-items: flex-start;
    }

    .tickets-table-wrap table tbody td[data-label="Subject"]::before {
        min-width: unset;
        margin-bottom: 4px;
    }

    .tickets-table-wrap table tbody td[data-label="Subject"] a {
        white-space: normal !important;
        overflow: visible !important;
        text-overflow: unset !important;
        max-width: 100%;
    }

    .tickets-table-wrap table tbody td[data-label="Action"]::before {
        display: none;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.simslayout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/tickets/index.blade.php ENDPATH**/ ?>