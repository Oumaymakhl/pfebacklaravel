<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // Fetch the receiver
    $receiver = User::where('id', '!=', auth()->id())->first();

    // Pass the receiver's ID to the view
    return view('welcome', ['receiverId' => $receiver->id]);
});

Route::post('/send-message', [App\Http\Controllers\ChatController::class, 'sendMessage']);
Route::get('/get-messages/{senderId}/{receiverId}', [App\Http\Controllers\ChatController::class, 'getMessages']);



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');







