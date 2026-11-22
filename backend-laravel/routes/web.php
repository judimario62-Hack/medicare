<?php

use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/connexion', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/connexion', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');

    Route::get('/inscription', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisteredUserController::class, 'store'])->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/tableau-de-bord', DashboardController::class)->name('dashboard');
    Route::view('/administration', 'dashboard.admin')
        ->middleware('role:admin')
        ->name('admin.index');
    Route::post('/deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications.index');

    Route::get('/consultations/{appointment}/ordonnance-pdf', [AppointmentController::class, 'prescriptionPdf'])
        ->middleware('role:doctor')
        ->name('appointments.prescription.pdf');

    Route::get('/mon-dossier', [PatientController::class, 'myRecord'])
        ->middleware('role:patient')
        ->name('patient-record.show');

    Route::get('/mon-dossier/modifier', [PatientController::class, 'editMyRecord'])
        ->middleware('role:patient')
        ->name('patient-record.edit');

    Route::put('/mon-dossier', [PatientController::class, 'updateMyRecord'])
        ->middleware('role:patient')
        ->name('patient-record.update');

    Route::get('/patients/{patient}/dossier-medical', [PatientController::class, 'medicalFile'])
        ->middleware('role:admin,doctor,staff')
        ->name('patients.medical-file');

    Route::get('/consultations/{appointment}', [AppointmentController::class, 'consultation'])
        ->middleware('role:doctor')
        ->name('appointments.consultation');

    Route::post('/consultations/{appointment}/store', [AppointmentController::class, 'storeConsultation'])
        ->middleware('role:doctor')
        ->name('appointments.consultation.store');

    Route::middleware('role:admin,doctor,staff,patient')->group(function (): void {
        Route::resource('appointments', AppointmentController::class);
        Route::get('/demandes-a-valider', [AppointmentController::class, 'pendingForValidation'])
            ->middleware('role:admin,staff')
            ->name('appointments.pending');
        Route::patch('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm'])
            ->middleware('role:admin,staff')
            ->name('appointments.confirm');
        Route::patch('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])
            ->middleware('role:admin,staff')
            ->name('appointments.reject');
        Route::get('/agenda-medecin', [AppointmentController::class, 'agenda'])
            ->middleware('role:doctor')
            ->name('appointments.agenda');
    });

    Route::middleware('role:admin,doctor,staff')->group(function (): void {
        Route::resource('patients', PatientController::class);
        Route::get('/assistant-ia', [AiAssistantController::class, 'index'])->name('assistant.index');
        Route::post('/assistant-ia/generate', [AiAssistantController::class, 'generate'])->name('assistant.generate');
        Route::get('/assistant-ia/{aiRequest}', [AiAssistantController::class, 'result'])->name('assistant.result');
        Route::get('/assistant-ia/{aiRequest}/ordonnance-pdf', [AiAssistantController::class, 'prescriptionPdf'])->name('assistant.prescription.pdf');
    });

    Route::middleware('role:admin,staff')->group(function (): void {
        Route::resource('rooms', RoomController::class);
        Route::resource('equipment', EquipmentController::class);
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('doctors', DoctorController::class);
    });
});
