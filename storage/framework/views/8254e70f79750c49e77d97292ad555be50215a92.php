<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Invoice <?php echo e($order->order_number); ?></title>
<style>
  @page { size: A4 portrait; margin: 0; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 12px;
    line-height: 1.45;
    color: #1a1a1a;
    background: #ffffff;
    padding: 28px 36px;
  }
  table { border-collapse: collapse; width: 100%; }
  td, th { vertical-align: top; }
  .num { text-align: right; }
</style>
</head>
<body>

<?php
    $storeName    = $appStoreName ?? 'AlbertinaNG';
    $storeAddr    = $storeAddress ?? 'No. 22 Zik Avenue, Uwani, Enugu State, Nigeria';
    $storeContact = $storeEmail   ?? 'support@albertinang.com';
    $storePhone   = $storePhone   ?? null;

    $isPickup      = $order->fulfillment_method === 'pickup';
    $customerName  = $order->user->name  ?? 'Guest';
    $customerEmail = $order->user->email ?? $order->customer_email ?? '';
    $customerPhone = $order->user?->phone_no ?? null;

    // Base64 logo — DomPDF cannot load external URLs, must embed
    $logoFilePath = !empty($appStoreLogo)
        ? public_path('storage/' . $appStoreLogo)
        : public_path('image.png');

    if (!empty($appStoreLogo) && !file_exists($logoFilePath)) {
        $logoFilePath = public_path('image.png');
    }

    $logoSrc = null;
    if (file_exists($logoFilePath)) {
        $ext     = strtolower(pathinfo($logoFilePath, PATHINFO_EXTENSION));
        $mimeMap = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml'];
        $mime    = $mimeMap[$ext] ?? 'image/png';
        $data    = file_get_contents($logoFilePath);
        if ($data !== false) {
            $logoSrc = 'data:' . $mime . ';base64,' . base64_encode($data);
        }
    }

    $installTotal  = $order->items->sum(fn($i) => $i->installation_extra_ngn * $i->quantity);
    $itemsBase     = $order->items->sum(fn($i) => $i->price * $i->quantity);
    $baseOnlyTotal = $itemsBase - $installTotal;
    $discount      = (float) ($order->coupon_discount_ngn ?? 0);
    $deliveryFee   = (float) ($order->shipping_cost ?? 0);
    $grandTotal    = (float) $order->total;

    $invAccent   = $invAccent   ?? '#1a7a4a';
    $invAccentBg = $invAccentBg ?? '#e8f5ee';

    // Admin-configured body section order (per fulfilment mode). The PDF header
    // stays a fixed table — dompdf cannot reliably free-position elements — but
    // the section ORDER below honours the saved layout.
    $defaultBody = ['bill_to','fulfillment','items','notes_totals','bank','terms','footer'];
    $bodyOrder   = ($invLayout['body'] ?? null) ?: $defaultBody;

    $mintBg   = 'background-color:' . $invAccentBg . ';';
    $mintFill = 'background-color:' . $invAccentBg . ';';
    $hdrStyle = $mintBg . 'color:' . $invAccent . ';font-weight:bold;font-size:10px;text-transform:uppercase;letter-spacing:0.5px;padding:7px 14px;border-bottom:1px solid #d0d0d0;';
    $cellP    = 'padding:12px 14px;font-size:11.5px;line-height:1.7;';
    $itemHdr  = 'background-color:#1f2937;color:#ffffff;font-size:10px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;padding:8px 10px;border:1px solid #1f2937;border-bottom:0;';
    $itemCell = 'padding:7px 10px;border:1px solid #e0e0e0;font-size:11.5px;';
    $instCell = 'background-color:#f7fdf9;border:1px solid #e0e0e0;border-top:0;padding:2px 10px 6px 10px;font-size:10.5px;';
?>


<table style="margin-bottom:16px;">
  <tr>
    <td style="width:55%;vertical-align:middle;">
      <?php if($logoSrc): ?>
        <img src="<?php echo e($logoSrc); ?>" style="height:60px;width:auto;max-width:220px;display:block;" alt="<?php echo e($storeName); ?>">
      <?php else: ?>
        <span style="font-size:22px;font-weight:bold;color:<?php echo e($invAccent); ?>;"><?php echo e($storeName); ?></span>
      <?php endif; ?>
    </td>
    <td style="width:45%;vertical-align:middle;text-align:right;">
      <span style="font-size:24px;font-weight:bold;color:<?php echo e($invAccent); ?>;letter-spacing:2px;">INVOICE</span><br>
      <span style="font-size:10px;color:#888;line-height:1.7;">
        <?php echo e($storeName); ?><?php if($storeAddr): ?> | <?php echo e($storeAddr); ?><?php endif; ?><br>
        <?php echo e($storeContact); ?><?php if($storePhone): ?> | <?php echo e($storePhone); ?><?php endif; ?>
        <?php if(!empty($invHeaderNote)): ?><br><em><?php echo e($invHeaderNote); ?></em><?php endif; ?>
      </span>
    </td>
  </tr>
</table>


<p style="text-align:right;font-weight:bold;font-size:12px;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:16px;">
  Issue Date: <?php echo e($order->created_at->format('d F Y')); ?>

</p>


<table style="margin-bottom:0;">
  <tr>
    <td style="background-color:<?php echo e($invAccent); ?>;height:26px;font-size:1px;line-height:26px;">&nbsp;</td>
  </tr>
</table>


<?php $__currentLoopData = $bodyOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<?php if($sec === 'bill_to'): ?>

<table style="border:1px solid #d0d0d0;margin-bottom:0;">
  <tr>
    <td style="width:50%;<?php echo e($hdrStyle); ?>">Bill To</td>
    <td style="width:50%;<?php echo e($hdrStyle); ?>text-align:right;border-left:1px solid #d0d0d0;">Invoice Details</td>
  </tr>
  <tr>
    <td style="<?php echo e($cellP); ?>">
      <strong><?php echo e($customerName); ?></strong><br>
      <?php if($order->user?->shipping_address): ?><?php echo e($order->user->shipping_address); ?><br><?php endif; ?>
      <?php if($order->user?->city): ?><?php echo e($order->user->city); ?><br><?php endif; ?>
      <?php if($order->user?->state): ?><?php echo e($order->user->state); ?><br><?php endif; ?>
      <?php if($customerEmail): ?><span style="color:#555;"><?php echo e($customerEmail); ?></span><br><?php endif; ?>
      <?php if($customerPhone): ?><span style="color:#555;"><?php echo e($customerPhone); ?></span><?php endif; ?>
    </td>
    <td style="<?php echo e($cellP); ?>text-align:right;border-left:1px solid #d0d0d0;">
      <span style="color:#555;">Order No.:</span> <?php echo e($order->order_number); ?><br>
      <span style="color:#555;">Currency:</span> NGN (&#8358;)<br>
      <span style="color:#555;">Payment:</span> <?php echo e(ucfirst(str_replace('_', ' ', $order->payment_method))); ?><br>
      <?php if($order->reference): ?><span style="color:#555;">Reference:</span> <?php echo e($order->reference); ?><br><?php endif; ?>
      <?php if($order->coupon_code_used): ?><span style="color:#555;">Coupon:</span> <?php echo e($order->coupon_code_used); ?><?php endif; ?>
    </td>
  </tr>
</table>

<?php elseif($sec === 'fulfillment'): ?>

<table style="border:1px solid #d0d0d0;margin-top:10px;margin-bottom:10px;">
  <tr>
    <td style="<?php echo e($hdrStyle); ?>"><?php echo e($isPickup ? 'Collection From' : 'Delivery To'); ?></td>
  </tr>
  <tr>
    <td style="<?php echo e($cellP); ?>">
      <?php if($isPickup): ?>
        <strong><?php echo e($order->pickup_point_name ?: $storeName . ' Collection Point'); ?></strong><br>
        <?php if($order->pickup_point_address): ?><?php echo e($order->pickup_point_address); ?><br><?php endif; ?>
        <?php if($order->pickup_location): ?><?php echo e($order->pickup_location); ?><br><?php endif; ?>
        <?php echo e($storeContact); ?>

      <?php else: ?>
        <strong><?php echo e($customerName); ?></strong><br>
        <?php if($order->shipping_address): ?><?php echo e($order->shipping_address); ?><br><?php endif; ?>
        <?php if($order->delivery_location_name): ?><?php echo e($order->delivery_location_name); ?>, <?php endif; ?>
        <?php if($order->delivery_state_name): ?><?php echo e($order->delivery_state_name); ?><br><?php endif; ?>
        Nigeria<br>
        <?php if($customerEmail): ?><?php echo e($customerEmail); ?><?php endif; ?>
        <?php if($customerPhone): ?><br><?php echo e($customerPhone); ?><?php endif; ?>
      <?php endif; ?>
    </td>
  </tr>
</table>

<?php elseif($sec === 'items'): ?>

<table style="margin-top:10px;margin-bottom:10px;">
  <thead>
    <tr>
      <td style="<?php echo e($itemHdr); ?>width:5%;">#</td>
      <td style="<?php echo e($itemHdr); ?>width:51%;">Item Description</td>
      <td style="<?php echo e($itemHdr); ?>width:8%;text-align:right;">Qty</td>
      <td style="<?php echo e($itemHdr); ?>width:18%;text-align:right;">Unit Price</td>
      <td style="<?php echo e($itemHdr); ?>width:18%;text-align:right;">Amount</td>
    </tr>
  </thead>
  <tbody>
    <?php $rowNum = 0; ?>
    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
          $rowNum++;
          $basePrice  = $item->price - $item->installation_extra_ngn;
          $baseAmount = $basePrice * $item->quantity;
          $instAmount = $item->installation_extra_ngn * $item->quantity;
      ?>
      <tr>
        <td style="<?php echo e($itemCell); ?>"><?php echo e($rowNum); ?></td>
        <td style="<?php echo e($itemCell); ?>">
          <?php echo e($item->name); ?>

          <?php if($item->sku): ?><br><span style="font-size:10.5px;color:#888;">SKU: <?php echo e($item->sku); ?></span><?php endif; ?>
        </td>
        <td style="<?php echo e($itemCell); ?>text-align:right;"><?php echo e($item->quantity); ?></td>
        <td style="<?php echo e($itemCell); ?>text-align:right;">&#8358;<?php echo e(number_format($basePrice, 2)); ?></td>
        <td style="<?php echo e($itemCell); ?>text-align:right;">&#8358;<?php echo e(number_format($baseAmount, 2)); ?></td>
      </tr>
      <?php if($item->installation_option && $item->installation_extra_ngn > 0): ?>
        <tr>
          <td style="<?php echo e($instCell); ?>"></td>
          <td style="<?php echo e($instCell); ?>padding-left:28px;color:<?php echo e($invAccent); ?>;">+ Installation: <?php echo e($item->installation_option); ?></td>
          <td style="<?php echo e($instCell); ?>text-align:right;color:#555;"><?php echo e($item->quantity); ?></td>
          <td style="<?php echo e($instCell); ?>text-align:right;color:#555;">&#8358;<?php echo e(number_format($item->installation_extra_ngn, 2)); ?></td>
          <td style="<?php echo e($instCell); ?>text-align:right;color:#555;">&#8358;<?php echo e(number_format($instAmount, 2)); ?></td>
        </tr>
      <?php elseif($item->installation_option): ?>
        <tr>
          <td style="<?php echo e($instCell); ?>"></td>
          <td style="<?php echo e($instCell); ?>padding-left:28px;color:<?php echo e($invAccent); ?>;" colspan="4">+ Installation: <?php echo e($item->installation_option); ?> (included)</td>
        </tr>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </tbody>
</table>

<?php elseif($sec === 'notes_totals'): ?>

<table style="margin-top:10px;margin-bottom:10px;">
  <tr>
    <td style="<?php echo e($mintFill); ?>padding:12px 14px;font-size:11px;line-height:1.75;width:55%;vertical-align:top;">
      <strong style="font-size:10px;color:<?php echo e($invAccent); ?>;text-transform:uppercase;letter-spacing:0.4px;">Order Notes</strong><br><br>
      Fulfilment: <?php echo e($isPickup ? 'Customer collection' : 'Home delivery'); ?><br>
      <?php if(!$isPickup && $order->delivery_state_name): ?>
        Delivery to: <?php echo e($order->delivery_location_name ? $order->delivery_location_name . ', ' : ''); ?><?php echo e($order->delivery_state_name); ?><br>
      <?php endif; ?>
      Order reference: <?php echo e($order->order_number); ?><br>
      <?php if($order->coupon_code_used): ?>Coupon applied: <?php echo e($order->coupon_code_used); ?><br><?php endif; ?>
      Customer support: <?php echo e($storeContact); ?>

    </td>
    <td style="width:14px;"></td>
    <td style="<?php echo e($mintFill); ?>padding:12px 14px;width:43%;vertical-align:top;">
      <table style="width:100%;">
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Items Subtotal</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">&#8358;<?php echo e(number_format($baseOnlyTotal, 2)); ?></td>
        </tr>
        <?php if($installTotal > 0): ?>
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Installation</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">+&#8358;<?php echo e(number_format($installTotal, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($deliveryFee > 0): ?>
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Delivery Fee</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">+&#8358;<?php echo e(number_format($deliveryFee, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($discount > 0): ?>
        <tr>
          <td style="color:#333;font-weight:bold;padding:2px 0;font-size:11px;">Discount</td>
          <td style="text-align:right;color:#1a1a1a;padding:2px 0;font-size:11px;">-&#8358;<?php echo e(number_format($discount, 2)); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
          <td style="color:#1a1a1a;font-weight:bold;font-size:12px;border-top:2px solid #1a1a1a;padding-top:6px;">Total Due</td>
          <td style="text-align:right;color:#1a1a1a;font-weight:bold;font-size:12px;border-top:2px solid #1a1a1a;padding-top:6px;">&#8358;<?php echo e(number_format($grandTotal, 2)); ?></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<?php elseif($sec === 'bank'): ?>

<?php if(!empty($invBankDetails)): ?>
<table style="margin-top:12px;margin-bottom:12px;">
  <tr>
    <td style="border:1px solid #d0d0d0;padding:10px 14px;font-size:10.5px;line-height:1.8;color:#444;">
      <strong style="font-size:11px;color:#1a1a1a;">Bank Details</strong><br>
      <?php echo nl2br(e($invBankDetails)); ?>

    </td>
  </tr>
</table>
<?php endif; ?>

<?php elseif($sec === 'terms'): ?>

<table style="margin-top:12px;margin-bottom:12px;">
  <tr>
    <td style="border:1px solid #d0d0d0;padding:10px 14px;font-size:10.5px;line-height:1.8;color:#444;">
      <strong style="font-size:11px;color:#1a1a1a;">Terms &amp; Conditions</strong><br>
      <?php if(!empty($invTerms)): ?>
        <?php echo nl2br(e($invTerms)); ?>

      <?php else: ?>
        1. This invoice is evidence of your order and payment record. Please retain it for your records.<br>
        2. Goods may be returned or exchanged only in accordance with the <?php echo e($storeName); ?> Returns Policy; eligibility depends on product condition and category.<br>
        3. Report delivery issues or damaged goods within 48 hours of receipt via <?php echo e($storeContact); ?>, quoting your order number.<br>
        4. Prices are in Nigerian Naira (&#8358;).<br>
        5. This invoice is generated electronically and requires no signature.
      <?php endif; ?>
    </td>
  </tr>
</table>

<?php elseif($sec === 'footer'): ?>

<p style="text-align:center;color:#888;font-size:12px;margin-top:8px;">
  <?php if(!empty($invFooter)): ?>
    <?php echo nl2br(e($invFooter)); ?>

  <?php else: ?>
    Thank you for shopping with <?php echo e($storeName); ?>. | <?php echo e($storeContact); ?>

  <?php endif; ?>
</p>
<?php endif; ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</body>
</html>
<?php /**PATH C:\Users\BSEMA\Downloads\Ecommerce_zip\resources\views/sims/invoice-pdf.blade.php ENDPATH**/ ?>