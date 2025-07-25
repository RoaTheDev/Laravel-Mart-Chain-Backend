<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/




// Auth
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});

// Category
Route::group(['middleware' => 'api', 'prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Branch
Route::group(['middleware' => 'api', 'prefix' => 'branch'], function () {
    Route::get('/lists', [BranchController::class, 'lists'])->name('branch.lists');
    Route::get('/{id}', [BranchController::class, 'show'])->name('branch.show');
    Route::post('/create', [BranchController::class, 'create'])->name('branch.create');
    Route::put('/{id}', [BranchController::class, 'update'])->name('branch.update');
    Route::delete('/{id}', [BranchController::class, 'delete'])->name('branch.delete');
    Route::post('/restore', [BranchController::class, 'restore'])->name('branch.restore');
});

// Product
Route::group(['middleware' => 'api', 'prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [ProductController::class, 'delete'])->name('products.delete');
    Route::post('/restore', [ProductController::class, 'restore'])->name('products.restore');
});


// Position
Route::group(['middleware' => 'api', 'prefix' => 'positions'], function () {
    Route::get('/', [PositionController::class, 'index'])->name('positions.index');
    Route::get('/{id}', [PositionController::class, 'show'])->name('positions.show');
    Route::post('/', [PositionController::class, 'store'])->name('positions.store');
    Route::put('/{id}', [PositionController::class, 'update'])->name('positions.update');
    Route::delete('/{id}', [PositionController::class, 'delete'])->name('positions.delete');
    Route::post('/restore', [PositionController::class, 'restore'])->name('positions.restore');
});

// Invoice
Route::group(['middleware' => 'api', 'prefix' => 'invoices'], function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/{id}', [InvoiceController::class, 'delete'])->name('invoices.delete');
    Route::post('/restore', [InvoiceController::class, 'restore'])->name('invoices.restore');
});


// Invoice Item
Route::group(['middleware' => 'api', 'prefix' => 'invoice-items'], function () {
    Route::get('/', [InvoiceItemController::class, 'index'])->name('invoice-items.index');
    Route::get('/{id}', [InvoiceItemController::class, 'show'])->name('invoice-items.show');
    Route::post('/', [InvoiceItemController::class, 'store'])->name('invoice-items.store');
    Route::put('/{id}', [InvoiceItemController::class, 'update'])->name('invoice-items.update');
    Route::delete('/{id}', [InvoiceItemController::class, 'delete'])->name('invoice-items.delete');
    Route::post('/restore', [InvoiceItemController::class, 'restore'])->name('invoice-items.restore');
});

//Staff
Route::group(['middleware' => 'api', 'prefix' => 'staff'], function () {
    Route::get('/', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/{id}', [StaffController::class, 'show'])->name('staff.show');
    Route::post('/', [StaffController::class, 'store'])->name('staff.store');
    Route::put('/{id}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/{id}', [StaffController::class, 'delete'])->name('staff.delete');
    Route::post('/restore', [StaffController::class, 'restore'])->name('staff.restore');
});
