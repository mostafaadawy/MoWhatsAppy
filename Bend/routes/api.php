<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\ApiAuthController;
use App\Http\Controllers\APIs\UsersApisController;
use App\Http\Controllers\APIs\ChatController;
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
Route::get('getAllUsers', [UsersApisController::class,'getAllUsers'])->name('getAllUsers');
Route::get('checkIfUserExists/{search}', [UsersApisController::class,'checkIfUserExists'])->name('checkIfUserExists');
Route::post('saveUserDetails', [UsersApisController::class,'saveUserDetails'])->name('saveUserDetails');
// Create a new chat and add message
Route::post('createChatSendMessage', [ChatController::class,'createChatSendMessage'])->name('createChatSendMessage');
// Send a message to an existing chat
Route::post('sendMessageToExistingChat', [ChatController::class,'sendMessageToExistingChat'])->name('sendMessageToExistingChat');
// Edit a message
Route::put('editMessage/{messageId}', [ChatController::class,'editMessage'])->name('editMessage');
// Delete a message
Route::delete('deleteMessage/{messageId}', [ChatController::class, 'deleteMessage'])->name('deleteMessage');
// Delete a chat along with its messages
Route::delete('deleteChat/{chatId}', [ChatController::class, 'deleteChat'])->name('deleteChat');
});
