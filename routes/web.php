<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\IncidentController;

// Welcome Page with Dynamic Carousel
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
})->name('login');

Route::post('/register/create', [UserController::class, 'register']);
Route::post('/login-success', [UserController::class,'login']);
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
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Customer\CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Pet Management
    Route::get('/pets', [App\Http\Controllers\Customer\PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/create', [App\Http\Controllers\Customer\PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [App\Http\Controllers\Customer\PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/{id}', [App\Http\Controllers\Customer\PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{id}/edit', [App\Http\Controllers\Customer\PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{id}', [App\Http\Controllers\Customer\PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{id}', [App\Http\Controllers\Customer\PetController::class, 'destroy'])->name('pets.destroy');
    
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
});

// Veterinarian Routes
Route::prefix('veterinarian')->name('veterinarian.')->group(function () {
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
    Route::post('/appointments/{id}/claim', [App\Http\Controllers\Veterinarian\VeterinarianController::class, 'claimAppointment'])->name('appointments.claim');
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
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin.role'])->group(function () {
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

    // Admin Users
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Pets
    Route::resource('pets', PetController::class);
    Route::get('/pets/search', [PetController::class, 'search'])->name('pets.search');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    
    // Inventory Management
    Route::resource('inventory', InventoryController::class);

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
        Route::get('/{medicalRecord}/edit', [MedicalRecordController::class, 'edit'])->name('edit');
        Route::put('/{medicalRecord}', [MedicalRecordController::class, 'update'])->name('update');
        Route::delete('/{medicalRecord}', [MedicalRecordController::class, 'destroy'])->name('destroy');
        Route::get('/pet/{pet}', [MedicalRecordController::class, 'byPet'])->name('pet');
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
        Route::get('/pet/{pet}', [PrescriptionController::class, 'byPet'])->name('pet');
        Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
        Route::get('/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('edit');
        Route::put('/{prescription}', [PrescriptionController::class, 'update'])->name('update');
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('destroy');
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
        });

        // Lab Requisitions (Requests / Results)
        Route::prefix('requisitions')->name('requisitions.')->group(function () {
            Route::get('/create', [LaboratoryController::class, 'requisitionsCreate'])->name('create');
            Route::post('/', [LaboratoryController::class, 'requisitionsStore'])->name('store');
            Route::get('/{labRequisition}', [LaboratoryController::class, 'requisitionsShow'])->name('show');
            Route::get('/{labRequisition}/edit', [LaboratoryController::class, 'requisitionsEdit'])->name('edit');
            Route::put('/{labRequisition}', [LaboratoryController::class, 'requisitionsUpdate'])->name('update');
            Route::delete('/{labRequisition}', [LaboratoryController::class, 'requisitionsDestroy'])->name('destroy');
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
    });

    // Grooming
    Route::resource('grooming', GroomingController::class);
    
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
        Route::get('/alerts', [PharmacyController::class, 'alerts'])->name('alerts');
        // Dynamic routes come after specific routes
        Route::post('/', [PharmacyController::class, 'store'])->name('store');
        Route::get('/{id}', [PharmacyController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PharmacyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PharmacyController::class, 'update'])->name('update');
        Route::delete('/{id}', [PharmacyController::class, 'destroy'])->name('destroy');
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
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/{id}/payment', [BillingController::class, 'paymentForm'])->name('payment');
        Route::post('/{id}/payment', [BillingController::class, 'processPayment'])->name('payment.process');
        Route::get('/generate-from-appointment/{appointmentId}', [BillingController::class, 'generateFromAppointment'])->name('generate.from.appointment');
        Route::post('/{id}/send', [BillingController::class, 'sendInvoice'])->name('send');
        Route::post('/{id}/mark-overdue', [BillingController::class, 'markOverdue'])->name('mark.overdue');
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
            Route::get('/filter', [StaffController::class, 'filter'])->name('filter');
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