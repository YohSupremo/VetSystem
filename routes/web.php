<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\UserController;
use App\Models\ClinicSetting;
use App\Models\Pet;
use App\Models\User;
use App\Models\Appointment;

// Include test routes
require __DIR__.'/test-db.php';
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PetController;
use App\Http\Controllers\Admin\PetOwnerController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\VaccinationController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\BoardingController;
use App\Http\Controllers\Admin\GroomingController;
use App\Http\Controllers\Admin\PharmacyController;
use App\Http\Controllers\Admin\LaboratoryController;
use App\Http\Controllers\Admin\SurgeryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\IncidentController;
use App\Http\Controllers\Admin\ChronicConditionController;
use App\Http\Controllers\Admin\PetAllergyController;

// Welcome Page with Dynamic Carousel
Route::get('/', function () {
    $carouselImages = [];
    $landingStats = [
        'pets' => 0,
        'veterinarians' => 0,
        'appointments' => 0,
    ];
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

    try {
        $landingStats['pets'] = Pet::count();
        $landingStats['veterinarians'] = User::where('role', 'veterinarian')->count();
        $landingStats['appointments'] = Appointment::count();
    } catch (\Throwable $e) {
    }

    return view('welcome', compact('carouselImages', 'landingStats'));
});

Route::get('/learn-more', function () {
    $clinicSetting = ClinicSetting::current();

    return view('learn-more', compact('clinicSetting'));
})->name('learn-more');

// Public Pet QR View
Route::get('/pets/{id}/qr', [App\Http\Controllers\Customer\PetController::class, 'publicQr'])
    ->name('pets.qr.public');
Route::get('/pets/{id}/scan-medical-records', [App\Http\Controllers\Customer\PetController::class, 'publicMedicalRecords'])
    ->name('pets.qr.records');

// Authentication Routes
Route::get('/register', function(){
    return view('register');
});

Route::get('/login', function(){
    return view('login');
})->name('login');

Route::post('/register/create', [UserController::class, 'register']);
Route::post('/login-success', [UserController::class,'login']);

Route::get('/email/verify', function (Request $request) {
    $email = (string) $request->session()->get('verification_email', '');

    return view('verify-email', compact('email'));
})->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
    ]);

    $user = User::where('email', $validated['email'])->first();

    $request->session()->put('verification_email', $validated['email']);

    if (! $user) {
        return back()->withErrors([
            'email' => 'We could not find an account for that email address.',
        ]);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('/login')->with('success', 'This email is already verified. You can log in now.');
    }

    $mailUsername = (string) env('MAIL_USERNAME', '');
    $mailPassword = (string) env('MAIL_PASSWORD', '');
    if (
        str_contains($mailUsername, 'your_gmail@gmail.com') ||
        str_contains($mailPassword, 'your_gmail_app_password') ||
        $mailUsername === '' ||
        $mailPassword === ''
    ) {
        return back()->withErrors([
            'email' => 'Mail server is not configured yet. Replace MAIL_USERNAME and MAIL_PASSWORD in .env with your real Gmail and Google App Password.',
        ]);
    }

    try {
        $user->sendEmailVerificationNotification();
    } catch (\Throwable $e) {
        report($e);

        $errorText = $e->getMessage();
        $isAuthFailure = str_contains($errorText, 'BadCredentials') || str_contains($errorText, 'Failed to authenticate on SMTP server');

        $fallbackLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        return back()
            ->withErrors([
                'email' => $isAuthFailure
                    ? 'Gmail SMTP login failed. Generate a new Google App Password for the sender account and update MAIL_PASSWORD.'
                    : 'Unable to send verification email right now. Please check SMTP credentials in .env and try again.',
            ])
            ->with('verification_link', $fallbackLink);
    }

    return back()->with('success', 'A new verification link has been sent to your email.');
})->middleware('throttle:6,1')->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    $request->session()->forget('verification_email');

    return redirect('/login')->with('success', 'Email verified successfully. You may now log in.');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::get('/dashboard', function(){
    return view('dashboard');
});

Route::get('/logout', function() {
    \Illuminate\Support\Facades\Auth::logout();
    session()->forget('username');
    return redirect('/login');
});

Route::get('/reports', function () {
    return redirect()->route('admin.reports.index');
});

// Customer Routes
Route::prefix('customer')->name('customer.')->middleware(['auth.flash'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Customer\CustomerDashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/{id}/read', [App\Http\Controllers\Customer\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [App\Http\Controllers\Customer\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread-count', [App\Http\Controllers\Customer\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
    
    // Pet Management
    Route::get('/pets', [App\Http\Controllers\Customer\PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/create', [App\Http\Controllers\Customer\PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [App\Http\Controllers\Customer\PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{id}', [App\Http\Controllers\Customer\PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{id}/edit', [App\Http\Controllers\Customer\PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{id}', [App\Http\Controllers\Customer\PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{id}', [App\Http\Controllers\Customer\PetController::class, 'destroy'])->name('pets.destroy');
    Route::get('/pets/{id}/qr', [App\Http\Controllers\Customer\PetController::class, 'qrCode'])->name('pets.qr');
    Route::get('/pets/{id}/qr/records', [App\Http\Controllers\Customer\PetController::class, 'qrMedicalRecords'])->name('pets.qr-records');
    Route::get('/prescriptions/pets/{petId}/{prescriptionId}/print', [App\Http\Controllers\Customer\PetController::class, 'printPrescription'])->name('prescriptions.print');
    
    // Appointments
    Route::get('/appointments', [App\Http\Controllers\Customer\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [App\Http\Controllers\Customer\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\Customer\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{id}', [App\Http\Controllers\Customer\AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{id}/edit', [App\Http\Controllers\Customer\AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{id}', [App\Http\Controllers\Customer\AppointmentController::class, 'update'])->name('appointments.update');
    Route::post('/appointments/{id}/cancel', [App\Http\Controllers\Customer\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // Medical Records
    Route::get('/medical-records', [App\Http\Controllers\Customer\MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('/medical-records/pets/{petId}', [App\Http\Controllers\Customer\MedicalRecordController::class, 'petRecords'])->name('medical-records.pet');
    Route::get('/medical-records/pets/{petId}/records/{recordId}', [App\Http\Controllers\Customer\MedicalRecordController::class, 'show'])->name('medical-records.show');
    
    // Chronic Conditions CRUD
    Route::post('/medical-records/pets/{petId}/chronic-conditions', [App\Http\Controllers\Customer\MedicalRecordController::class, 'storeChronicCondition'])->name('medical-records.chronic-conditions.store');
    Route::put('/medical-records/pets/{petId}/chronic-conditions/{conditionId}', [App\Http\Controllers\Customer\MedicalRecordController::class, 'updateChronicCondition'])->name('medical-records.chronic-conditions.update');
    Route::delete('/medical-records/pets/{petId}/chronic-conditions/{conditionId}', [App\Http\Controllers\Customer\MedicalRecordController::class, 'destroyChronicCondition'])->name('medical-records.chronic-conditions.destroy');
    
    // Pet Allergies CRUD
    Route::post('/medical-records/pets/{petId}/allergies', [App\Http\Controllers\Customer\MedicalRecordController::class, 'storeAllergy'])->name('medical-records.allergies.store');
    Route::put('/medical-records/pets/{petId}/allergies/{allergyId}', [App\Http\Controllers\Customer\MedicalRecordController::class, 'updateAllergy'])->name('medical-records.allergies.update');
    Route::delete('/medical-records/pets/{petId}/allergies/{allergyId}', [App\Http\Controllers\Customer\MedicalRecordController::class, 'destroyAllergy'])->name('medical-records.allergies.destroy');

    // Incident Reports
    Route::get('/incidents', [App\Http\Controllers\Customer\IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/create', [App\Http\Controllers\Customer\IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [App\Http\Controllers\Customer\IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{id}', [App\Http\Controllers\Customer\IncidentController::class, 'show'])->name('incidents.show');
    
    // Products / Shop
    Route::get('/products', [App\Http\Controllers\Customer\ProductController::class, 'index'])->name('products.index');
    Route::post('/products/{productId}/order', [App\Http\Controllers\Customer\ProductController::class, 'order'])->name('products.order');
    Route::post('/products/{productId}/add-to-cart', [App\Http\Controllers\Customer\ProductController::class, 'addToCart'])->name('products.add-to-cart');
    
    // Shopping Cart
    Route::get('/cart', [App\Http\Controllers\Customer\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update/{itemId}', [App\Http\Controllers\Customer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{itemId}', [App\Http\Controllers\Customer\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [App\Http\Controllers\Customer\CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/checkout', [App\Http\Controllers\Customer\CartController::class, 'checkout'])->name('cart.checkout');
    
    // Billing
    Route::get('/billing', [App\Http\Controllers\Customer\BillingController::class, 'index'])->name('billing.index');
    Route::get('/billing/{id}', [App\Http\Controllers\Customer\BillingController::class, 'show'])->name('billing.show');
    Route::get('/billing/{id}/pay', [App\Http\Controllers\Customer\BillingController::class, 'pay'])->name('billing.pay');
    Route::post('/billing/{id}/pay', [App\Http\Controllers\Customer\BillingController::class, 'processPayment'])->name('billing.process-payment');
    Route::get('/billing/receipts/{paymentId}', [App\Http\Controllers\Customer\BillingController::class, 'receipt'])->name('billing.receipt');
    
    // Orders
    Route::get('/orders', [App\Http\Controllers\Customer\BillingController::class, 'orders'])->name('billing.orders');
    Route::get('/orders/{orderId}', [App\Http\Controllers\Customer\BillingController::class, 'orderDetails'])->name('billing.order-details');
    Route::post('/orders/{orderId}/cancel', [App\Http\Controllers\Customer\BillingController::class, 'cancelOrder'])->name('billing.cancel-order');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Customer\CustomerController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\Customer\CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/deactivate', [App\Http\Controllers\Customer\CustomerController::class, 'deactivateAccount'])->name('profile.deactivate');
});

// Veterinarian Routes
Route::prefix('veterinarian')->name('veterinarian.')->middleware(['auth.flash'])->group(function () {
    Route::get('/test', function() {
        return view('veterinarian.test');
    });
    Route::get('/dashboard', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'appointments'])->name('appointments.index');
    Route::get('/appointments/create', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'createAppointment'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'storeAppointment'])->name('appointments.store');
    Route::get('/appointments/{id}', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'showAppointment'])->name('appointments.show');
    Route::post('/appointments/{id}/status', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'updateAppointmentStatus'])->name('appointments.update-status');
    Route::post('/appointments/{id}/cancel', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::get('/patients', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'patients'])->name('patients.index');
    Route::get('/patients/{id}', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'showPatient'])->name('patients.show');
    Route::get('/medical-records', [App\Http\Controllers\Veterinarian\MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('/medical-records/pets/{petId}/create', [App\Http\Controllers\Veterinarian\MedicalRecordController::class, 'create'])->name('medical-records.create');
    Route::post('/medical-records/pets/{petId}', [App\Http\Controllers\Veterinarian\MedicalRecordController::class, 'store'])->name('medical-records.store');
    Route::get('/medical-records/pets/{petId}/{recordId}', [App\Http\Controllers\Veterinarian\MedicalRecordController::class, 'show'])->name('medical-records.show');
    Route::get('/medical-records/pets/{petId}/{recordId}/edit', [App\Http\Controllers\Veterinarian\MedicalRecordController::class, 'edit'])->name('medical-records.edit');
    Route::put('/medical-records/pets/{petId}/{recordId}', [App\Http\Controllers\Veterinarian\MedicalRecordController::class, 'update'])->name('medical-records.update');
    Route::get('/prescriptions', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/pets/{petId}/create', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions/pets/{petId}', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/pets/{petId}/{prescriptionId}', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/pets/{petId}/{prescriptionId}/edit', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'edit'])->name('prescriptions.edit');
    Route::put('/prescriptions/pets/{petId}/{prescriptionId}', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::post('/prescriptions/pets/{petId}/{prescriptionId}/status', [App\Http\Controllers\Veterinarian\PrescriptionController::class, 'updateStatus'])->name('prescriptions.update-status');
    Route::get('/vaccinations', [App\Http\Controllers\Veterinarian\VaccinationController::class, 'index'])->name('vaccinations.index');
    Route::get('/vaccinations/pets/{petId}/create', [App\Http\Controllers\Veterinarian\VaccinationController::class, 'create'])->name('vaccinations.create');
    Route::post('/vaccinations/pets/{petId}', [App\Http\Controllers\Veterinarian\VaccinationController::class, 'store'])->name('vaccinations.store');
    Route::get('/vaccinations/pets/{petId}/{vaccinationId}', [App\Http\Controllers\Veterinarian\VaccinationController::class, 'show'])->name('vaccinations.show');
    Route::get('/vaccinations/pets/{petId}/{vaccinationId}/edit', [App\Http\Controllers\Veterinarian\VaccinationController::class, 'edit'])->name('vaccinations.edit');
    Route::put('/vaccinations/pets/{petId}/{vaccinationId}', [App\Http\Controllers\Veterinarian\VaccinationController::class, 'update'])->name('vaccinations.update');
    Route::get('/laboratory', [App\Http\Controllers\Veterinarian\LaboratoryController::class, 'index'])->name('laboratory.index');
    Route::get('/laboratory/pets/{petId}/create', [App\Http\Controllers\Veterinarian\LaboratoryController::class, 'create'])->name('laboratory.create');
    Route::post('/laboratory/pets/{petId}', [App\Http\Controllers\Veterinarian\LaboratoryController::class, 'store'])->name('laboratory.store');
    Route::get('/laboratory/pets/{petId}/{testId}', [App\Http\Controllers\Veterinarian\LaboratoryController::class, 'show'])->name('laboratory.show');
    Route::get('/laboratory/pets/{petId}/{testId}/edit', [App\Http\Controllers\Veterinarian\LaboratoryController::class, 'edit'])->name('laboratory.edit');
    Route::put('/laboratory/pets/{petId}/{testId}', [App\Http\Controllers\Veterinarian\LaboratoryController::class, 'update'])->name('laboratory.update');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\Veterinarian\NotificationController::class, 'index'])->name('index');
        Route::get('/get', [App\Http\Controllers\Veterinarian\NotificationController::class, 'getNotifications'])->name('get');
        Route::post('/{id}/read', [App\Http\Controllers\Veterinarian\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [App\Http\Controllers\Veterinarian\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\Veterinarian\NotificationController::class, 'delete'])->name('delete');
        Route::get('/settings', [App\Http\Controllers\Veterinarian\NotificationController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [App\Http\Controllers\Veterinarian\NotificationController::class, 'updateSettings'])->name('settings-update');
    });
    Route::get('/unread-count', [App\Http\Controllers\Veterinarian\NotificationController::class, 'getUnreadCount'])->name('unread-count');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth.flash', 'admin.role'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/global-search', [DashboardController::class, 'globalSearch'])->name('global-search');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/get', [NotificationController::class, 'getNotifications'])->name('get');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
        Route::get('/settings', [NotificationController::class, 'settings'])->name('settings');
        Route::post('/settings/update', [NotificationController::class, 'updateSettings'])->name('settings-update');
        Route::post('/delete-old', [NotificationController::class, 'deleteOld'])->name('delete-old');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
    
    // Pet Owners
    Route::resource('pet-owners', PetOwnerController::class);
    Route::get('/pet-owners/search', [PetOwnerController::class, 'search'])->name('pet-owners.search');
    Route::post('/pet-owners/{id}/restore', [PetOwnerController::class, 'restore'])->name('pet-owners.restore');

    // Admin Users
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('/users/{id}/restore', [App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');
    
    // Staff Schedules
    Route::prefix('staff-schedules')->name('staff-schedules.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\StaffScheduleController::class, 'index'])->name('index');
        Route::get('/{userId}/edit', [App\Http\Controllers\Admin\StaffScheduleController::class, 'edit'])->name('edit');
        Route::put('/{userId}', [App\Http\Controllers\Admin\StaffScheduleController::class, 'update'])->name('update');
        Route::post('/toggle', [App\Http\Controllers\Admin\StaffScheduleController::class, 'toggle'])->name('toggle');
    });
    
    // Pets
    Route::resource('pets', PetController::class);
    Route::get('/pets/search', [PetController::class, 'search'])->name('pets.search');
    Route::post('/pets/{id}/restore', [PetController::class, 'restore'])->name('pets.restore');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/assign', [AppointmentController::class, 'assign'])->name('appointments.assign');
    Route::post('/appointments/{id}/restore', [AppointmentController::class, 'restore'])->name('appointments.restore');
    
    // Inventory Management
    Route::resource('inventory', InventoryController::class);
    Route::post('/inventory/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{orderId}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // Queue Management
    Route::prefix('queue')->name('queue.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\QueueManagementController::class, 'index'])->name('index');
        Route::get('/data', [App\Http\Controllers\Admin\QueueManagementController::class, 'getQueueData'])->name('data');
        Route::get('/veterinarian/{veterinarianId}', [App\Http\Controllers\Admin\QueueManagementController::class, 'getVeterinarianQueue'])->name('veterinarian');
        Route::post('/call-next/{veterinarianId?}', [App\Http\Controllers\Admin\QueueManagementController::class, 'callNext'])->name('call-next');
        Route::get('/stats', [App\Http\Controllers\Admin\QueueManagementController::class, 'getQueueStats'])->name('stats');

        // Queue CRUD-style pages (non-JS)
        Route::get('/create', [App\Http\Controllers\Admin\QueueManagementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\QueueManagementController::class, 'store'])->name('store');
        Route::get('/{appointment}', [App\Http\Controllers\Admin\QueueManagementController::class, 'show'])->name('show');
        Route::get('/{appointment}/edit', [App\Http\Controllers\Admin\QueueManagementController::class, 'edit'])->name('edit');
        Route::put('/{appointment}/status', [App\Http\Controllers\Admin\QueueManagementController::class, 'updateStatus'])->name('status.update');
    });

    // Medical Records
    Route::prefix('medical-records')->name('medical-records.')->group(function () {
        Route::get('/', [MedicalRecordController::class, 'index'])->name('index');
        Route::get('/dashboard', [\App\Http\Controllers\Admin\MedicalRecordDashboardController::class, 'index'])->name('dashboard');
        Route::get('/create', [MedicalRecordController::class, 'create'])->name('create');
        Route::post('/', [MedicalRecordController::class, 'store'])->name('store');
        Route::get('/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('show');
        Route::post('/{medicalRecord}/mark-chronic', [MedicalRecordController::class, 'markAsChronic'])->name('mark-chronic');
        Route::get('/{medicalRecord}/edit', [MedicalRecordController::class, 'edit'])->name('edit');
        Route::put('/{medicalRecord}', [MedicalRecordController::class, 'update'])->name('update');
        Route::delete('/{medicalRecord}', [MedicalRecordController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [MedicalRecordController::class, 'restore'])->name('restore');
        Route::get('/pet/{pet}', [MedicalRecordController::class, 'byPet'])->name('pet');
    });

    // Chronic Conditions
    Route::prefix('chronic-conditions')->name('chronic-conditions.')->group(function () {
        Route::get('/', [ChronicConditionController::class, 'index'])->name('index');
        Route::get('/pet/{pet}', [ChronicConditionController::class, 'byPet'])->name('pet');
        Route::get('/create', [ChronicConditionController::class, 'create'])->name('create');
        Route::post('/', [ChronicConditionController::class, 'store'])->name('store');
        Route::get('/{chronicCondition}', [ChronicConditionController::class, 'show'])->name('show');
        Route::get('/{chronicCondition}/edit', [ChronicConditionController::class, 'edit'])->name('edit');
        Route::put('/{chronicCondition}', [ChronicConditionController::class, 'update'])->name('update');
        Route::delete('/{chronicCondition}', [ChronicConditionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ChronicConditionController::class, 'restore'])->name('restore');
    });

    // Pet Allergies
    Route::prefix('pet-allergies')->name('pet-allergies.')->group(function () {
        Route::get('/', [PetAllergyController::class, 'index'])->name('index');
        Route::get('/pet/{pet}', [PetAllergyController::class, 'byPet'])->name('pet');
        Route::get('/create', [PetAllergyController::class, 'create'])->name('create');
        Route::post('/', [PetAllergyController::class, 'store'])->name('store');
        Route::get('/{petAllergy}', [PetAllergyController::class, 'show'])->name('show');
        Route::get('/{petAllergy}/edit', [PetAllergyController::class, 'edit'])->name('edit');
        Route::put('/{petAllergy}', [PetAllergyController::class, 'update'])->name('update');
        Route::delete('/{petAllergy}', [PetAllergyController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [PetAllergyController::class, 'restore'])->name('restore');
    });

    // Incident Reports
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::get('/', [IncidentController::class, 'index'])->name('index');
        Route::get('/create', [IncidentController::class, 'create'])->name('create');
        Route::post('/', [IncidentController::class, 'store'])->name('store');
        Route::get('/{id}', [IncidentController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [IncidentController::class, 'edit'])->name('edit');
        Route::put('/{id}', [IncidentController::class, 'update'])->name('update');
        Route::put('/{id}/status', [IncidentController::class, 'updateStatus'])->name('status-update');
        Route::delete('/{id}', [IncidentController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [IncidentController::class, 'restore'])->name('restore');
    });

    // Vaccinations
    Route::prefix('vaccinations')->name('vaccinations.')->group(function () {
        Route::get('/', [VaccinationController::class, 'index'])->name('index');
        Route::get('/create', [VaccinationController::class, 'create'])->name('create');
        Route::get('/available-veterinarians', [VaccinationController::class, 'getAvailableVeterinarians'])->name('available-veterinarians');
        Route::post('/appointments/{appointment}/accept', [VaccinationController::class, 'acceptAppointment'])->name('appointments.accept');
        Route::post('/', [VaccinationController::class, 'store'])->name('store');
        Route::post('/{vaccination}/payment', [VaccinationController::class, 'processPayment'])->name('payment.process');
        Route::get('/{vaccination}', [VaccinationController::class, 'show'])->name('show');
        Route::get('/{vaccination}/edit', [VaccinationController::class, 'edit'])->name('edit');
        Route::put('/{vaccination}', [VaccinationController::class, 'update'])->name('update');
        Route::delete('/{vaccination}', [VaccinationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [VaccinationController::class, 'restore'])->name('restore');
        Route::get('/pet/{pet}', [VaccinationController::class, 'byPet'])->name('pet');
    });

    // Prescriptions
    Route::prefix('prescriptions')->name('prescriptions.')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index'])->name('index');
        Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
        Route::post('/', [PrescriptionController::class, 'store'])->name('store');
        Route::get('/pet/{pet}', [PrescriptionController::class, 'byPet'])->name('pet');
        Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
        Route::get('/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('edit');
        Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('update');
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [PrescriptionController::class, 'restore'])->name('restore');
    });

    // Laboratory (schema-based: lab_tests + lab_requisitions)
    Route::prefix('laboratory')->name('laboratory.')->group(function () {
        // Dashboard
        Route::get('/', [LaboratoryController::class, 'dashboard'])->name('index');

        // Lab Tests (Catalog)
        Route::prefix('tests')->name('tests.')->group(function () {
            Route::get('/', [LaboratoryController::class, 'testsIndex'])->name('index');
            Route::get('/create', [LaboratoryController::class, 'testsCreate'])->name('create');
            Route::post('/', [LaboratoryController::class, 'testsStore'])->name('store');
            Route::get('/{labTest}', [LaboratoryController::class, 'testsShow'])->name('show');
            Route::get('/{labTest}/edit', [LaboratoryController::class, 'testsEdit'])->name('edit');
            Route::put('/{labTest}', [LaboratoryController::class, 'testsUpdate'])->name('update');
            Route::delete('/{labTest}', [LaboratoryController::class, 'testsDestroy'])->name('destroy');
            Route::post('/{id}/restore', [LaboratoryController::class, 'testsRestore'])->name('restore');
        });

        // Lab Requisitions (Requests / Results)
        Route::prefix('requisitions')->name('requisitions.')->group(function () {
            Route::get('/create', [LaboratoryController::class, 'requisitionsCreate'])->name('create');
            Route::post('/', [LaboratoryController::class, 'requisitionsStore'])->name('store');
            Route::post('/{id}/mark-paid', [LaboratoryController::class, 'markRequisitionPaid'])->name('mark-paid');
            Route::get('/{labRequisition}', [LaboratoryController::class, 'requisitionsShow'])->name('show');
            Route::get('/{labRequisition}/edit', [LaboratoryController::class, 'requisitionsEdit'])->name('edit');
            Route::put('/{labRequisition}', [LaboratoryController::class, 'requisitionsUpdate'])->name('update');
            Route::delete('/{labRequisition}', [LaboratoryController::class, 'requisitionsDestroy'])->name('destroy');
            Route::post('/{id}/restore', [LaboratoryController::class, 'requisitionsRestore'])->name('restore');
        });
    });

    // Boarding
    Route::prefix('boarding')->name('boarding.')->group(function(){
        Route::get('/', [BoardingController::class, 'index'])->name('index');
        Route::get('/new-boarding', [BoardingController::class, 'create'])->name('new-boarding');
        Route::post('/new-boarding', [BoardingController::class, 'createPass'])->name('new-boarding.store');
        Route::post('/{boarding}/invoice', [BoardingController::class, 'generateInvoice'])->name('invoice.generate');
        Route::post('/{boarding}/payment', [BoardingController::class, 'processPayment'])->name('payment.process');
        
        // RESTful routes
        Route::get('/create', [BoardingController::class, 'create'])->name('create');
        Route::post('/', [BoardingController::class, 'store'])->name('store');
        Route::get('/{boarding}', [BoardingController::class, 'show'])->name('show');
        Route::get('/{boarding}/edit', [BoardingController::class, 'edit'])->name('edit');
        Route::put('/{boarding}', [BoardingController::class, 'update'])->name('update');
        Route::delete('/{boarding}', [BoardingController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [BoardingController::class, 'restore'])->name('restore');

        // Boarding Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Boarding\NotificationController::class, 'index'])->name('index');
            Route::get('/get', [App\Http\Controllers\Boarding\NotificationController::class, 'getNotifications'])->name('get');
            Route::post('/{id}/read', [App\Http\Controllers\Boarding\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/read-all', [App\Http\Controllers\Boarding\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [App\Http\Controllers\Boarding\NotificationController::class, 'delete'])->name('delete');
            Route::get('/settings', [App\Http\Controllers\Boarding\NotificationController::class, 'settings'])->name('settings');
            Route::post('/settings/update', [App\Http\Controllers\Boarding\NotificationController::class, 'updateSettings'])->name('settings-update');
        });
        Route::get('/unread-count', [App\Http\Controllers\Boarding\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Grooming
    Route::prefix('grooming')->name('grooming.')->group(function () {
        Route::get('/appointment/{appointment}/complete', [GroomingController::class, 'completeFromAppointment'])->name('complete');
        Route::post('/appointment/{appointment}/complete', [GroomingController::class, 'storeFromAppointment'])->name('complete.store');
        Route::post('/{grooming}/mark-paid', [GroomingController::class, 'markPaid'])->name('mark-paid');
        Route::get('/', [GroomingController::class, 'index'])->name('index');
        Route::get('/create', [GroomingController::class, 'create'])->name('create');
        Route::post('/', [GroomingController::class, 'store'])->name('store');
        Route::get('/{grooming}', [GroomingController::class, 'show'])->name('show');
        Route::get('/{grooming}/edit', [GroomingController::class, 'edit'])->name('edit');
        Route::put('/{grooming}', [GroomingController::class, 'update'])->name('update');
        Route::delete('/{grooming}', [GroomingController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [GroomingController::class, 'restore'])->name('restore');

        // Grooming Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Groomer\NotificationController::class, 'index'])->name('index');
            Route::get('/get', [App\Http\Controllers\Groomer\NotificationController::class, 'getNotifications'])->name('get');
            Route::post('/{id}/read', [App\Http\Controllers\Groomer\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/read-all', [App\Http\Controllers\Groomer\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [App\Http\Controllers\Groomer\NotificationController::class, 'delete'])->name('delete');
            Route::get('/settings', [App\Http\Controllers\Groomer\NotificationController::class, 'settings'])->name('settings');
            Route::post('/settings/update', [App\Http\Controllers\Groomer\NotificationController::class, 'updateSettings'])->name('settings-update');
        });
        Route::get('/unread-count', [App\Http\Controllers\Groomer\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
    
    // Grooming Services Management
    Route::prefix('grooming-services')->name('grooming-services.')->group(function () {
        Route::get('/', [GroomingController::class, 'servicesIndex'])->name('index');
        Route::get('/create', [GroomingController::class, 'servicesCreate'])->name('create');
        Route::post('/', [GroomingController::class, 'servicesStore'])->name('store');
        Route::get('/{service}', [GroomingController::class, 'servicesShow'])->name('show');
        Route::get('/{service}/edit', [GroomingController::class, 'servicesEdit'])->name('edit');
        Route::put('/{service}', [GroomingController::class, 'servicesUpdate'])->name('update');
        Route::delete('/{service}', [GroomingController::class, 'servicesDestroy'])->name('destroy');
    });

    // Pharmacy

    Route::prefix('pharmacy')->name('pharmacy.')->group(function () {
        Route::get('/', [PharmacyController::class, 'index'])->name('index');
        Route::get('/create', [PharmacyController::class, 'create'])->name('create');
        // Specific routes must come before dynamic {id} routes
        Route::get('/dispense', [PharmacyController::class, 'dispenseForm'])->name('dispense');
        Route::post('/dispense', [PharmacyController::class, 'dispense'])->name('dispense.store');
        Route::get('/dispensing-history', [PharmacyController::class, 'dispensingHistory'])->name('dispensing.history');
        Route::post('/dispensing-history/{id}/mark-paid', [PharmacyController::class, 'markDispensingPaid'])->name('dispensing.mark-paid');
        Route::get('/alerts', [PharmacyController::class, 'alerts'])->name('alerts');
        // Dynamic routes come after specific routes
        Route::post('/', [PharmacyController::class, 'store'])->name('store');
        Route::get('/{id}', [PharmacyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PharmacyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PharmacyController::class, 'update'])->name('update');
        Route::delete('/{id}', [PharmacyController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [PharmacyController::class, 'restore'])->name('restore');

        // Pharmacy Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Pharmacy\NotificationController::class, 'index'])->name('index');
            Route::get('/get', [App\Http\Controllers\Pharmacy\NotificationController::class, 'getNotifications'])->name('get');
            Route::post('/{id}/read', [App\Http\Controllers\Pharmacy\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/read-all', [App\Http\Controllers\Pharmacy\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [App\Http\Controllers\Pharmacy\NotificationController::class, 'delete'])->name('delete');
            Route::get('/settings', [App\Http\Controllers\Pharmacy\NotificationController::class, 'settings'])->name('settings');
            Route::post('/settings/update', [App\Http\Controllers\Pharmacy\NotificationController::class, 'updateSettings'])->name('settings-update');
        });
        Route::get('/unread-count', [App\Http\Controllers\Pharmacy\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Surgeries
    Route::prefix('surgeries')->name('surgeries.')->group(function () {
        Route::get('/', [SurgeryController::class, 'index'])->name('index');
        Route::get('/create', [SurgeryController::class, 'create'])->name('create');
        Route::get('/available-surgeons', [SurgeryController::class, 'getAvailableSurgeons'])->name('available-surgeons');
        Route::post('/', [SurgeryController::class, 'store'])->name('store');
        Route::post('/{surgery}/payment', [SurgeryController::class, 'processPayment'])->name('payment.process');
        Route::get('/{surgery}', [SurgeryController::class, 'show'])->name('show');
        Route::get('/{surgery}/edit', [SurgeryController::class, 'edit'])->name('edit');
        Route::put('/{surgery}', [SurgeryController::class, 'update'])->name('update');
        Route::delete('/{surgery}', [SurgeryController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [SurgeryController::class, 'restore'])->name('restore');
        Route::get('/pet/{pet}', [SurgeryController::class, 'byPet'])->name('pet');
    });

    // Cages
    Route::resource('cages', \App\Http\Controllers\Admin\CageController::class);
    Route::get('/cages/scan/{code}', [\App\Http\Controllers\Admin\CageController::class, 'scan'])->name('cages.scan');
    Route::post('/cages/{id}/release', [\App\Http\Controllers\Admin\CageController::class, 'release'])->name('cages.release');

// Customer Routes
    Route::get('prescriptions/pet/{petId}', [PrescriptionController::class, 'byPet'])->name('prescriptions.pet');

    // Billing
    Route::resource('billing', BillingController::class);
    Route::post('/billing/{id}/restore', [BillingController::class, 'restore'])->name('billing.restore');
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/{id}/payment', [BillingController::class, 'paymentForm'])->name('payment');
        Route::post('/{id}/payment', [BillingController::class, 'processPayment'])->name('payment.process');
        Route::get('/generate-from-appointment/{appointmentId}', [BillingController::class, 'generateFromAppointment'])->name('generate.from.appointment');
        Route::post('/{id}/send', [BillingController::class, 'sendInvoice'])->name('send');
        Route::post('/{id}/mark-overdue', [BillingController::class, 'markOverdue'])->name('mark.overdue');
    });

    // Reception
    Route::prefix('reception')->name('reception.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('index');
        
        // Reception Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Reception\NotificationController::class, 'index'])->name('index');
            Route::get('/get', [App\Http\Controllers\Reception\NotificationController::class, 'getNotifications'])->name('get');
            Route::post('/{id}/read', [App\Http\Controllers\Reception\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/read-all', [App\Http\Controllers\Reception\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [App\Http\Controllers\Reception\NotificationController::class, 'delete'])->name('delete');
            Route::get('/settings', [App\Http\Controllers\Reception\NotificationController::class, 'settings'])->name('settings');
            Route::post('/settings/update', [App\Http\Controllers\Reception\NotificationController::class, 'updateSettings'])->name('settings-update');
        });
        Route::get('/unread-count', [App\Http\Controllers\Reception\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Staff
    Route::prefix('staff')->name('staff.')->group(function(){
            Route::get('/create', [StaffController::class, 'create'])->name('create');
            Route::get('/', [StaffController::class, 'index'])->name('index');
            Route::post('/store', [StaffController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [StaffController::class, 'update'])->name('update');
            Route::get('/{id}/info', [StaffController::class, 'show'])->name('info');
            Route::delete('/destroy/{id}', [StaffController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [StaffController::class, 'restore'])->name('restore');
            Route::get('/filter', [StaffController::class, 'filter'])->name('filter');

            // Staff Notifications
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [App\Http\Controllers\Staff\NotificationController::class, 'index'])->name('index');
                Route::get('/get', [App\Http\Controllers\Staff\NotificationController::class, 'getNotifications'])->name('get');
                Route::post('/{id}/read', [App\Http\Controllers\Staff\NotificationController::class, 'markAsRead'])->name('mark-read');
                Route::post('/read-all', [App\Http\Controllers\Staff\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
                Route::delete('/{id}', [App\Http\Controllers\Staff\NotificationController::class, 'delete'])->name('delete');
                Route::get('/settings', [App\Http\Controllers\Staff\NotificationController::class, 'settings'])->name('settings');
                Route::post('/settings/update', [App\Http\Controllers\Staff\NotificationController::class, 'updateSettings'])->name('settings-update');
            });
            Route::get('/unread-count', [App\Http\Controllers\Staff\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/financial', [ReportController::class, 'financialReport'])->name('financial');
        Route::get('/cancelled-invoices', [ReportController::class, 'cancelledInvoices'])->name('cancelled-invoices');
        Route::get('/medical', [ReportController::class, 'medicalReport'])->name('medical');
        Route::get('/inventory', [ReportController::class, 'inventoryReport'])->name('inventory');
        Route::get('/client', [ReportController::class, 'clientReport'])->name('client');
        Route::get('/appointment', [ReportController::class, 'appointmentReport'])->name('appointment');
        Route::get('/export/{reportType}', [ReportController::class, 'exportReport'])->name('export');
    });

    // Settings
    Route::resource('settings', SettingController::class);
});