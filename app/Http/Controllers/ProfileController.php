<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information via API.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $request->user()->fill($request->validated());

            $request->user()->save();

            return response()->json([
                'message' => 'Profile updated successfully.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }
}