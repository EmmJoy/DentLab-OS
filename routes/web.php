<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LabTechnicianController;
use App\Http\Controllers\AdminCartController;

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Serve files from storage/app/public when symlink is unreliable (Windows/XAMPP)
Route::get('/storage/{path}', [AdminController::class, 'servePublic'])
    ->where('path', '.*')
    ->name('storage.proxy');

// Alternate proxy to avoid any Apache/static conflicts: /files/{path}
Route::get('/files/{path}', [AdminController::class, 'servePublic'])
    ->where('path', '.*')
    ->name('files.proxy');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Patient Management
    Route::get('/patients', [AdminController::class, 'patients'])->name('patients.index');
    Route::get('/patients/create', [AdminController::class, 'createPatient'])->name('patients.create');
    Route::post('/patients', [AdminController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patient:Predict3DId}', [AdminController::class, 'showPatient'])->name('patients.show');
    Route::get('/patients/{patient:Predict3DId}/edit', [AdminController::class, 'editPatient'])->name('patients.edit');
    Route::put('/patients/{patient:Predict3DId}', [AdminController::class, 'updatePatient'])->name('patients.update');
    Route::delete('/patients/{patient:Predict3DId}', [AdminController::class, 'deletePatient'])->name('patients.destroy');

    // Patients Shortlist (last 15 days)
    Route::get('/patients/shortlist/patients', [AdminController::class, 'shortlistPatients'])->name('patients.shortlist.patients');
    Route::get('/patients/shortlist/doctors', [AdminController::class, 'shortlistDoctors'])->name('patients.shortlist.doctors');

    // Patients Export (Excel)
    Route::get('/patients/export/all', [AdminController::class, 'exportPatientsAll'])->name('patients.export.all');
    Route::get('/patients/export/last-month', [AdminController::class, 'exportPatientsLastMonth'])->name('patients.export.last_month');
    Route::get('/patients/export/range', [AdminController::class, 'exportPatientsRange'])->name('patients.export.range');
    
    // Product Management
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('products.show');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.destroy');

    // Cart
    Route::get('/cart', [AdminCartController::class, 'index'])->name('cart.index');
    Route::post('/products/{product}/add-to-cart', [AdminCartController::class, 'add'])->name('cart.add');
    Route::post('/cart/items/{item}/update', [AdminCartController::class, 'update'])->name('cart.update');
    Route::post('/cart/items/{item}/delete', [AdminCartController::class, 'delete'])->name('cart.delete');
    Route::post('/cart/confirm', [AdminCartController::class, 'confirm'])->name('cart.confirm');
    Route::get('/cart/confirmed', [AdminCartController::class, 'confirmedList'])->name('cart.confirmed');
    
    // Product Request Management
    Route::post('/product-requests/{productRequest}/approve', [AdminController::class, 'approveProductRequest'])->name('product-requests.approve');
    Route::post('/product-requests/{productRequest}/reject', [AdminController::class, 'rejectProductRequest'])->name('product-requests.reject');
    
    // Payment Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
    Route::get('/payments/shortlist/last-15-days', [AdminController::class, 'paymentsShortlistLast15'])->name('payments.shortlist.last15');
    Route::get('/payments/export/all', [AdminController::class, 'exportAllPayments'])->name('payments.export.all');
    Route::get('/payments/export/range', [AdminController::class, 'exportPaymentsRange'])->name('payments.export.range');
    Route::get('/payments/installments/pending', [AdminController::class, 'paymentsInstallmentsPending'])->name('payments.installments.pending');
    Route::get('/payments/create', [AdminController::class, 'createPayment'])->name('payments.create');
    Route::post('/payments', [AdminController::class, 'storePayment'])->name('payments.store');

    // Payment Plan (Predict3DId-based)
    Route::get('/payments/plan', [AdminController::class, 'paymentPlanIndex'])->name('payments.plan.index');
    Route::get('/payments/plan/patient/{predict3dId}', [AdminController::class, 'findPatientByPredictId'])->name('payments.plan.find-patient');
    Route::get('/payments/plan/{predict3dId}', [AdminController::class, 'getPaymentPlan'])->name('payments.plan.get');
    Route::post('/payments/plan/{predict3dId}', [AdminController::class, 'savePaymentPlan'])->name('payments.plan.save');
    Route::post('/payments/plan/{predict3dId}/installment', [AdminController::class, 'addInstallmentPayment'])->name('payments.plan.add-installment');
    Route::put('/payments/plan/{predict3dId}/total', [AdminController::class, 'updatePlanTotal'])->name('payments.plan.update-total');
    
    // Lab Technician Management
    Route::get('/lab-technicians', [AdminController::class, 'labTechnicians'])->name('lab-technicians.index');
    Route::get('/lab-technicians/create', [AdminController::class, 'createLabTechnician'])->name('lab-technicians.create');
    Route::post('/lab-technicians', [AdminController::class, 'storeLabTechnician'])->name('lab-technicians.store');
    Route::get('/lab-technicians/{user}/edit', [AdminController::class, 'editLabTechnician'])->name('lab-technicians.edit');
    Route::put('/lab-technicians/{user}', [AdminController::class, 'updateLabTechnician'])->name('lab-technicians.update');
    Route::delete('/lab-technicians/{user}', [AdminController::class, 'deleteLabTechnician'])->name('lab-technicians.destroy');
    
    // Production Field
    Route::get('/production', [AdminController::class, 'productionIndex'])->name('production.index');
    Route::get('/production/patient/{predict3dId}', [AdminController::class, 'findPatientByPredictId'])
        ->name('production.find-patient');
    Route::post('/production/save-cases/{predict3dId}', [AdminController::class, 'savePatientCases'])
        ->name('production.save-cases');
    // Production Steps (Admin)
    Route::get('/production/steps/{predict3dId}', [AdminController::class, 'getProductionSteps'])
        ->name('production.steps.get');
    Route::post('/production/steps/{predict3dId}', [AdminController::class, 'saveProductionSteps'])
        ->name('production.steps.save');
});

// Lab Technician Routes
Route::middleware(['auth', 'lab_technician'])->prefix('lab')->name('lab.')->group(function () {
    Route::get('/dashboard', [LabTechnicianController::class, 'dashboard'])->name('dashboard');
    
    // Patient Management - Full CRUD access like admin
    Route::get('/patients', [LabTechnicianController::class, 'patients'])->name('patients.index');
    Route::get('/patients/create', [LabTechnicianController::class, 'createPatient'])->name('patients.create');
    Route::post('/patients', [LabTechnicianController::class, 'storePatient'])->name('patients.store');
    Route::get('/patients/{patient:Predict3DId}', [LabTechnicianController::class, 'showPatient'])->name('patients.show');
    Route::get('/patients/{patient:Predict3DId}/edit', [LabTechnicianController::class, 'editPatient'])->name('patients.edit');
    Route::put('/patients/{patient:Predict3DId}', [LabTechnicianController::class, 'updatePatient'])->name('patients.update');
    Route::delete('/patients/{patient:Predict3DId}', [LabTechnicianController::class, 'deletePatient'])->name('patients.destroy');
    
    // Product Request Management - Lab technicians can only request products
    Route::get('/request-cart', [LabTechnicianController::class, 'requestCart'])->name('request-cart.index');
    Route::get('/request-cart/create', [LabTechnicianController::class, 'createProductRequest'])->name('request-cart.create');
    Route::post('/request-cart', [LabTechnicianController::class, 'storeProductRequest'])->name('request-cart.store');
    Route::get('/request-cart/{productRequest}', [LabTechnicianController::class, 'showProductRequest'])->name('request-cart.show');
    
    // Inventory Management (Lab)
    Route::get('/inventory', [LabTechnicianController::class, 'inventory'])->name('inventory.index');
    Route::get('/inventory/create', [LabTechnicianController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory', [LabTechnicianController::class, 'storeInventory'])->name('inventory.store');
    Route::get('/inventory/{inventory}', [LabTechnicianController::class, 'showInventory'])->name('inventory.show');
    Route::get('/inventory/{inventory}/edit', [LabTechnicianController::class, 'editInventory'])->name('inventory.edit');
    Route::put('/inventory/{inventory}', [LabTechnicianController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/{inventory}', [LabTechnicianController::class, 'deleteInventory'])->name('inventory.destroy');
    
    // Payment Management - Full CRUD access like admin
    Route::get('/payments', [LabTechnicianController::class, 'payments'])->name('payments.index');
    Route::get('/payments/create', [LabTechnicianController::class, 'createPayment'])->name('payments.create');
    Route::post('/payments', [LabTechnicianController::class, 'storePayment'])->name('payments.store');
    Route::get('/payments/{payment}', [LabTechnicianController::class, 'showPayment'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [LabTechnicianController::class, 'editPayment'])->name('payments.edit');
    Route::put('/payments/{payment}', [LabTechnicianController::class, 'updatePayment'])->name('payments.update');
    Route::delete('/payments/{payment}', [LabTechnicianController::class, 'deletePayment'])->name('payments.destroy');
    

    // Production Field
    Route::get('/production', [LabTechnicianController::class, 'productionIndex'])->name('production.index');
    Route::get('/production/patient/{predict3dId}', [LabTechnicianController::class, 'findPatientByPredictId'])
        ->name('production.find-patient');
    Route::post('/production/save-cases/{predict3dId}', [LabTechnicianController::class, 'savePatientCases'])
        ->name('production.save-cases');
    // Production Steps (Lab)
    Route::get('/production/steps/{predict3dId}', [LabTechnicianController::class, 'getProductionSteps'])
        ->name('production.steps.get');
    Route::post('/production/steps/{predict3dId}', [LabTechnicianController::class, 'saveProductionSteps'])
        ->name('production.steps.save');

    // Products listing for lab
    Route::get('/products', [App\Http\Controllers\LabCartController::class, 'products'])->name('products.index');

    // Cart
    Route::get('/cart', [App\Http\Controllers\LabCartController::class, 'index'])->name('cart.index');
    Route::post('/products/{product}/add-to-cart', [App\Http\Controllers\LabCartController::class, 'add'])->name('cart.add');
    Route::post('/cart/items/{item}/update', [App\Http\Controllers\LabCartController::class, 'update'])->name('cart.update');
    Route::post('/cart/items/{item}/delete', [App\Http\Controllers\LabCartController::class, 'delete'])->name('cart.delete');
    Route::post('/cart/confirm', [App\Http\Controllers\LabCartController::class, 'confirm'])->name('cart.confirm');
});

// Debug route - remove after testing
Route::get('/debug/product-requests', function() {
    $requests = \App\Models\ProductRequest::all();
    $users = \App\Models\User::all();
    
    echo "<h2>Product Requests:</h2>";
    foreach($requests as $req) {
        echo "ID: {$req->id}, Name: {$req->name}, Requested by: {$req->requested_by}, Status: {$req->status}<br>";
    }
    
    echo "<h2>Users:</h2>";
    foreach($users as $user) {
        echo "ID: {$user->id}, Name: {$user->name}, Role: {$user->role}<br>";
    }
    
    return;
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
