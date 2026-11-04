<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerOrderController;
use App\Http\Controllers\AdminManagerOrdersController;
Route::middleware(['auth','manager'])->prefix('manager')->name('manager.')->group(function() {
 Route::get('sales', [\App\Http\Controllers\ManagerSalesController::class,'index'])->name('sales.index');
 Route::post('products/block-image', [\App\Http\Controllers\ManagerProductController::class,'uploadBlockImage'])->name('products.block-image');
 Route::get('products/create', [\App\Http\Controllers\ManagerProductController::class,'create'])->name('products.create');
 Route::post('products', [\App\Http\Controllers\ManagerProductController::class,'store'])->name('products.store');
 Route::get('orders', [ManagerOrderController::class,'index'])->name('orders.index');
 Route::get('orders/create', [ManagerOrderController::class,'create'])->name('orders.create');
 Route::post('orders', [ManagerOrderController::class,'store'])->name('orders.store');
 Route::get('orders/{order}', [ManagerOrderController::class,'show'])->name('orders.show');
 Route::post('orders/{order}/payment', [\App\Http\Controllers\ManagerOrderReviewController::class,'payment'])->name('orders.payment');
 Route::post('orders/{order}/reviews/{type}', [\App\Http\Controllers\ManagerOrderReviewController::class,'review'])->whereIn('type',['cancellation','return'])->name('orders.review');
 Route::patch('orders/{order}/details', [ManagerOrderController::class,'details'])->name('orders.details');
 Route::patch('orders/{order}/status', [ManagerOrderController::class,'status'])->name('orders.status');
 Route::post('orders/{order}/notes', [ManagerOrderController::class,'note'])->name('orders.notes');
 Route::post('orders/{order}/requests', [ManagerOrderController::class,'requestApproval'])->name('orders.requests');
 Route::post('orders/{order}/notify', [ManagerOrderController::class,'notify'])->name('orders.notify');
 Route::get('orders/{order}/invoice', [ManagerOrderController::class,'invoice'])->name('orders.invoice');
 Route::get('orders/{order}/invoice/download', [ManagerOrderController::class,'downloadInvoice'])->name('orders.invoice.download');
});
Route::middleware(['auth','role:admin'])->prefix('admin/order-management')->name('admin.order-management.')->group(function() {
 Route::get('/', [AdminManagerOrdersController::class,'index'])->name('index');
 Route::patch('{order}/assign', [AdminManagerOrdersController::class,'assign'])->name('assign');
 Route::patch('approvals/{approval}', [AdminManagerOrdersController::class,'review'])->name('review');
});
