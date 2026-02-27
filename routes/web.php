<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Tasks\TaskController;
use App\Http\Middleware\PreventBackHistory;
use App\Mail\EmailVerifiy;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Dashboard route
Route::middleware(['guest', PreventBackHistory::class])->get('/', [DashboardController::class,'guestView'])->name('dashboard.guest');


// Group routes that require auth + verified email
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'authView'])
        ->name('dashboard.auth');

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
        Route::post('/photo/delete', [ProfileController::class, 'removeProfilePhoto'])->name('profile.photo.delete');
    });
    //General Routes Protected

    // Task routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/view', [TaskController::class, 'calenderView'])->name('tasks.view');
        Route::get('/calender', [TaskController::class, 'calenderView'])->name('tasks.calender');
    });


    // Project routes
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/view', [ProjectController::class, 'view'])->name('projects.view');
        Route::get('/calender', [ProjectController::class, 'view'])->name('projects.calender');
    });


});



// Test email (no middleware required)
Route::get('/testemail', function () {
    $name = "John Doe";
    Mail::to('yousifzaki017@gmail.com')->send(new EmailVerifiy($name));
});

require __DIR__.'/auth.php';
