<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
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


Route::get('/admin/dashboard', function(){
return view('admin/dashboard');
}

)->name('admin.dashboard');
