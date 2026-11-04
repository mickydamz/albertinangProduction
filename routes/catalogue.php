<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CatalogueController,AdminCatalogueController,ManagerCatalogueSubmissionController};
Route::middleware(['auth',\App\Http\Middleware\CatalogueEditor::class])->prefix('catalogue')->name('catalogue.')->group(function () {
    Route::get('/',[CatalogueController::class,'index'])->name('dashboard');
    Route::get('/products/create',[CatalogueController::class,'create'])->name('create');
    Route::post('/products/{product}/draft',[CatalogueController::class,'start'])->name('start');
    Route::post('/submissions',[CatalogueController::class,'store'])->middleware('throttle:30,1')->name('store');
    Route::get('/submissions/{revision}',[CatalogueController::class,'edit'])->name('edit');
    Route::put('/submissions/{revision}',[CatalogueController::class,'update'])->middleware('throttle:30,1')->name('update');
    Route::post('/submissions/{revision}/withdraw',[CatalogueController::class,'withdraw'])->name('withdraw');
    Route::post('/submissions/{revision}/archive',[CatalogueController::class,'archive'])->name('archive');
    Route::post('/submissions/{revision}/refresh',[CatalogueController::class,'refreshBase'])->name('refresh');
    Route::post('/block-image',[CatalogueController::class,'upload'])->middleware('throttle:20,1')->name('block-image');
});
Route::middleware(['auth','role:admin'])->prefix('admin/catalogue')->name('admin.catalogue.')->group(function () {
    Route::get('/',[AdminCatalogueController::class,'index'])->name('index');
    Route::get('/{revision}',[AdminCatalogueController::class,'show'])->name('show');
    Route::post('/{revision}/review',[AdminCatalogueController::class,'review'])->name('review');
});
Route::post('/manager/products/{product}/submit',[ManagerCatalogueSubmissionController::class,'submit'])->middleware(['auth','manager'])->name('manager.products.submit');
