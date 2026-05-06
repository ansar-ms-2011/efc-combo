<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        logger('AuthenticatedSessionController store reached');
        $request->authenticate();

        try {
            $user = Auth::user();
            logger('User Logged In: ' . ($user ? $user->name : 'No user in Auth'));
            // 👇 Detect if 2FA required
            if ($user && isset($user->two_factor_secret) && $user->two_factor_secret) {
                Auth::logout();

                return response()->json([
                    'two_factor' => true,
                    'success' => false,
                    'message' => '2FA verification needed!',
                    'data' => null,
                    'user_id' => $user->id
                ]);
            }

            $request->session()->regenerate();

            $user = Auth::user();
            $user->role = $user->getRoleNames()->first();
            $user->permissions = $user->getPermissionsViaRoles()->pluck('name');
            $data['user'] = $user;
        } catch (\Exception $e) {
            logger('Error in store controller logic: ' . $e->getMessage());
            throw $e;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successfully!',
            'data' => $data
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
