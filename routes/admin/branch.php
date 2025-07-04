<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;

Route::get('/branch/lists', [BranchController::class, 'lists']);
Route::post('/branch/create', [BranchController::class, 'create']);
Route::post('/branch/update', [BranchController::class, 'update']);
Route::post('/branch/delete', [BranchController::class, 'delete']);
