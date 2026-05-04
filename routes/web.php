<?php

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
|
| Core authenticated routes live here. Additional route modules are loaded
| from bootstrap/app.php (e.g. routes/settings.php, routes/account.php).
| When adding large route groups, prefer new files under routes/ and
| register them in bootstrap/app.php so this file stays focused.
|
*/

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use App\Http\Controllers\UserController;
use App\Settings\SystemSettings;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $canRegister = true;
    try {
        $systemSettings = app(SystemSettings::class);
        $canRegister = $systemSettings->registration_enabled && Features::enabled(Features::registration());
    } catch (\Exception $e) {
        // If settings table doesn't exist yet, default to enabled
        $canRegister = Features::enabled(Features::registration());
    }

    return Inertia::render('welcome', [
        'canRegister' => $canRegister,
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::middleware([HandlePrecognitiveRequests::class])->group(function () {
        Route::resources([
            'users' => UserController::class,
            'roles' => RoleController::class,
        ]);
    });

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('api/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('api/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
});

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('user/password', [PasswordController::class, 'edit'])->name('user-password.edit');
    Route::put('user/password', [PasswordController::class, 'update'])->name('user-password.update');

    Route::get('two-factor', [TwoFactorAuthenticationController::class, 'show'])->name('two-factor.show');
});
