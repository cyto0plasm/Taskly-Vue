<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SochialiteController extends Controller
{
    public function redirect($provider){
        //  dd($provider);
    Log::info('Session id: '.session()->getId());
    Log::info('All session: '.json_encode(session()->all()));
    Log::info('Cookies: '.json_encode(request()->cookies->all()));

        return Socialite::driver($provider)->redirect();
    }


   public function callback($provider)
{
    try {
       Log::info('Before Socialite user()');
$providerUser = Socialite::driver($provider)->user();
        Log::info('Socialite user:', (array)$providerUser);

        $dbUser = User::firstOrCreate(
            ['email' => $providerUser->getEmail()],
            [
                'name' => $providerUser->getName() ?? 'No Name',
                'password' => Hash::make(Str::random(20)),
            ]
        );

        // // Send verification if new and email exists
        // if (!$dbUser->hasVerifiedEmail() && $dbUser->email) {
        //     $dbUser->sendEmailVerificationNotification();
        // }
if (!$dbUser->hasVerifiedEmail()) {
    $dbUser->markEmailAsVerified(); // trust social login email
}

        Auth::guard('web')->login($dbUser);
        return redirect()->route('dashboard');

    } catch (\Exception $e) {
        Log::error('Socialite callback error: ' . $e->getMessage());
        return redirect()->route('login')->with('error', 'Login failed: ' . $e->getMessage());
    }
}

}
