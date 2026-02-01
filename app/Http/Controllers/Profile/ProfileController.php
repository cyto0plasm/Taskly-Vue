<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        return view('profile.profile', [
            'user' => $request->user(),
            'request' => $request
        ]);
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }


    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // dd($request);
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.index')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

public function updateProfilePhoto(Request $request)
{
    $request->validate([
        'profile_photo' => 'nullable|image|max:2048', // optional
    ]);
/** @var \App\Models\User $user */
    $user = Auth::user();

    if ($request->hasFile('profile_photo')) {
        $file = $request->file('profile_photo');

        // Delete old photo if exists
        if ($user->profile_photo && Storage::disk('public')->exists('profile_photos/' . $user->profile_photo)) {
            Storage::disk('public')->delete('profile_photos/' . $user->profile_photo);
        }

        // Store new photo
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('profile_photos', $filename, 'public');

        // Save filename in DB
        $user->profile_photo_path = $filename;
        $user->save();

        return back()->with('success', 'Profile picture updated!');
    }

    return back()->with('error', 'No file uploaded.');
}

public function RemoveProfilePhoto(){
   /** @var \App\Models\User $user */ 
    $user=Auth::user();
    if($user->profile_photo_path && Storage::disk('public')->exists('profile_photo/'.$user->profile_photo_path)){
        Storage::disk('public')->delete('profile_photo/'.$user->profile_photo_path);
    }
    $user->profile_photo_path=null;
    $user->save();
    return back()->with('success',"profile picture removed");
}

}