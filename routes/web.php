<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PetOwnerController;
use App\Http\Controllers\Admin\PetController;

Route::get('/', function () {
    $carouselImages = [];
    $carouselDir = public_path('images/carousel');

    if (is_dir($carouselDir)) {
        $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

        foreach ($extensions as $extension) {
            foreach (glob($carouselDir . DIRECTORY_SEPARATOR . "*.{$extension}") ?: [] as $file) {
                $carouselImages[] = asset('images/carousel/' . basename($file));
            }
        }

        $carouselImages = array_values(array_unique($carouselImages));
    }

    return view('welcome', compact('carouselImages'));
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
})->name('admin.dashboard');

// Notification & Message Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/notifications/get', [NotificationController::class, 'getNotifications']);
    Route::get('/messages/get', [NotificationController::class, 'getMessages']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markNotificationAsRead']);
    Route::post('/messages/{id}/read', [NotificationController::class, 'markMessageAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllNotificationsAsRead']);
    Route::get('/unread-counts', [NotificationController::class, 'getUnreadCounts']);
    
    // Pet Owners Routes
    Route::resource('pet-owners', PetOwnerController::class);
    Route::get('/pet-owners/search', [PetOwnerController::class, 'search'])->name('pet-owners.search');
    
    // Pets Routes
    Route::resource('pets', PetController::class);
    Route::get('/pets/search', [PetController::class, 'search'])->name('pets.search');
});
