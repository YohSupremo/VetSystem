<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function(){
    return view('register');
});


Route::get('/login', function(){
    return view('login');
});

Route::post('/register/create', [UserController::class, 'register']);
Route::post('/login-success', [UserController::class,'login']);
Route::get('/dashboard', function(){
    return view('dashboard');
});