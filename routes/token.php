<?php
use Illuminate\Http\Request;

Route::middleware(['auth:sanctum', 'role:Super Admin'])->prefix('api')->group(function () {

    Route::get('/tokens', function (Request $request) {
        $query = $request->user()->tokens();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $query->latest();

        return $query->paginate($request->get('per_page', 10));
    });


    Route::post('/tokens', function (Request $request) {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $token = $request->user()->createToken(
            $request->token_name,
            ['read']
        );

        return response()->json([
            'token' => $token->plainTextToken,
        ], 201);
    });

    Route::delete('/tokens/{tokenId}', function (Request $request, $tokenId) {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return response()->json(['message' => 'Token revoked']);
    });
});
