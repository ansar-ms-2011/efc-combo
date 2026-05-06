<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

Route::middleware('auth:sanctum')->prefix('api')->group(function () {

    Route::post('/2fa/enable', function (Request $request) {
        $user = $request->user();

        $google2fa = new Google2FA();

        // 1. Generate secret
        $secret = $google2fa->generateSecretKey();

        // 2. Save an encrypted secret
        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        // 3. Create QR manually (IMPORTANT)
        $qrUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return response()->json([
            'qr_url' => $qrUrl,
            'secret' => $secret
        ]);
    });

    Route::post('/2fa/confirm', function (Request $request) {
        $request->validate([
            'code' => 'required'
        ]);

        $user = $request->user();

        $google2fa = new Google2FA();

        $secret = Crypt::decryptString($user->two_factor_secret);

        if (! $google2fa->verifyKey($secret, $request->code)) {
            throw ValidationException::withMessages([
                'code' => __('Invalid code provided.'),
            ]);
        }

        // generate recovery codes
        $codes = collect(range(1, 8))->map(fn () => str()->random(10))->toArray();

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ])->save();

        return response()->json([
            'message' => '2FA enabled',
            'recovery_codes' => $codes
        ]);
    });

    Route::post('/2fa/disable', function (Request $request) {

        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => '2FA disabled successfully'
        ]);
    });
});

