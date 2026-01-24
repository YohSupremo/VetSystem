<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PetController;
use App\Http\Controllers\Admin\PetOwnerController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\VaccinationController;
use App\Http\Controllers\Admin\PrescriptionController;

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

// Authentication Routes
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

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notification & Messages
    Route::get('/notifications/get', [NotificationController::class, 'getNotifications']);
    Route::get('/messages/get', [NotificationController::class, 'getMessages']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markNotificationAsRead']);
    Route::post('/messages/{id}/read', [NotificationController::class, 'markMessageAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllNotificationsAsRead']);
    Route::get('/unread-counts', [NotificationController::class, 'getUnreadCounts']);
    
    // Pet Owners
    Route::resource('pet-owners', PetOwnerController::class);
    Route::get('/pet-owners/search', [PetOwnerController::class, 'search'])->name('pet-owners.search');
    
    // Pets
    Route::resource('pets', PetController::class);
    Route::get('/pets/search', [PetController::class, 'search'])->name('pets.search');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    
    // Queue Management
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\QueueManagementController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Admin\QueueManagementController::class, 'getQueueData'])->name('data');
        Route::get('/veterinarian/{veterinarianId}', [App\Http\Controllers\Admin\QueueManagementController::class, 'getVeterinarianQueue'])->name('veterinarian');
        Route::post('/call-next/{veterinarianId?}', [App\Http\Controllers\Admin\QueueManagementController::class, 'callNext'])->name('call-next');
        Route::put('/{appointment}/status', [App\Http\Controllers\Admin\QueueManagementController::class, 'updateStatus'])->name('status.update');
        Route::get('/stats', [App\Http\Controllers\Admin\QueueManagementController::class, 'getQueueStats'])->name('stats');
    });

    // Medical Records
    Route::prefix('medical-records')->name('medical-records.')->group(function () {
        Route::get('/', [MedicalRecordController::class, 'index'])->name('index');
        Route::get('/create', [MedicalRecordController::class, 'create'])->name('create');
        Route::post('/', [MedicalRecordController::class, 'store'])->name('store');
        Route::get('/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('show');
        Route::get('/{medicalRecord}/edit', [MedicalRecordController::class, 'edit'])->name('edit');
        Route::put('/{medicalRecord}', [MedicalRecordController::class, 'update'])->name('update');
        Route::delete('/{medicalRecord}', [MedicalRecordController::class, 'destroy'])->name('destroy');
        Route::get('/pet/{pet}', [MedicalRecordController::class, 'byPet'])->name('pet');
    });

    // Vaccinations
    Route::prefix('vaccinations')->name('vaccinations.')->group(function () {
        Route::get('/', [VaccinationController::class, 'index'])->name('index');
        Route::get('/create', [VaccinationController::class, 'create'])->name('create');
        Route::post('/', [VaccinationController::class, 'store'])->name('store');
        Route::get('/{vaccination}', [VaccinationController::class, 'show'])->name('show');
        Route::get('/{vaccination}/edit', [VaccinationController::class, 'edit'])->name('edit');
        Route::put('/{vaccination}', [VaccinationController::class, 'update'])->name('update');
        Route::delete('/{vaccination}', [VaccinationController::class, 'destroy'])->name('destroy');
        Route::get('/pet/{pet}', [VaccinationController::class, 'byPet'])->name('pet');
    });

    // Prescriptions
    Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index'])->name('index');
        Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
        Route::post('/', [PrescriptionController::class, 'store'])->name('store');
        Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
        Route::get('/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('edit');
        Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('update');
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('destroy');
    });

    // Surgeries
    Route::prefix('surgeries')->name('surgeries.')->group(function () {
        Route::get('/', [SurgeryController::class, 'index'])->name('index');
        Route::get('/create', [SurgeryController::class, 'create'])->name('create');
        Route::post('/', [SurgeryController::class, 'store'])->name('store');
        Route::get('/{surgery}', [SurgeryController::class, 'show'])->name('show');
        Route::get('/{surgery}/edit', [SurgeryController::class, 'edit'])->name('edit');
        Route::put('/{surgery}', [SurgeryController::class, 'update'])->name('update');
        Route::delete('/{surgery}', [SurgeryController::class, 'destroy'])->name('destroy');
    });
});