<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\ApiAuthController;
use App\Http\Controllers\APIs\UsersApisController;
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

Route::post('register', [ApiAuthController::class, 'register']);
Route::post('login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Your other protected API routes here
// Route::post('getUserDetails/{userId}', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
Route::get('getUserDetails', [UsersApisController::class,'getUserDetails'])->name('getUserDetails');
});
