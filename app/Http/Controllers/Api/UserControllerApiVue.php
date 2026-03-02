<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserControllerApiVue extends Controller
{
public function show(Request $request)
{
    $user = $request->user();

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
          'profile_photo_url' => $user->profile_photo_path
            ? asset('storage/profile_photos/' . $user->profile_photo_path)
            : null,
        'preferences' => $user->preferences ?? [],
    ]);
}

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => $user->fresh(),
        ]);
    }

   public function updatePreferences(Request $request)
{
    $validated = $request->validate([
        'defaultView'         => 'nullable|string',
        'compactCards'        => 'nullable|boolean',
        'showCompletionRing'  => 'nullable|boolean',
        'weekStartsMonday'    => 'nullable|boolean',
        'deadlineWarningDays' => 'nullable|integer',
    ]);

    $user = $request->user();

    $user->update([
        'preferences' => array_merge(
            $user->preferences ?? [],
            $validated
        )
    ]);

    return response()->json([
        'message' => 'Preferences updated',
        'preferences' => $user->preferences,
    ]);
}
}
