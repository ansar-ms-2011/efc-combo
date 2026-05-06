<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/two-factor-challenge', function (Request $request) {

    $request->validate([
        'user_id' => 'required|exists:users,id',
        'code' => 'required|string|min:6|max:6',
    ]);

    $user = User::findOrFail($request->user_id);

    $google2fa = new Google2FA();

    $secret = Crypt::decryptString($user->two_factor_secret);

    $valid = $google2fa->verifyKey($secret, $request->code);

    if (! $valid) {
        throw ValidationException::withMessages([
            'code' => __('Invalid code provided.'),
        ]);
    }

    Auth::login($user);

    $user = Auth::user();
    $user->role = $user->getRoleNames()->first();
    $user->permissions = $user->getPermissionsViaRoles()->pluck('name');
    $data['user'] = $user;

    return response()->json([
        'two_factor' => true,
        'two_factor_passed'=> true,
        'success' => true,
        'message' => 'Login successfully!',
        'data' => $data
    ]);
});
