<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('chat.create');
});

// Chat Routes
Route::get('/chat/new', [ChatController::class, 'create'])->name('chat.create');
Route::get('/chat/{secretKey}', [ChatController::class, 'join'])->name('chat.join');
Route::get('/chat/{secretKey}/login', [ChatController::class, 'showLogin'])->name('chat.login');
Route::post('/chat/{secretKey}/login', [ChatController::class, 'login'])->name('chat.login.post');
Route::post('/chat/{secretKey}/message', [ChatController::class, 'sendMessage']);
Route::post('/chat/leave', [ChatController::class, 'leave'])->name('chat.leave');
