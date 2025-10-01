<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            Auth::login($user);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect('/dashboard');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Erreur Google Login');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->scopes(['email'])->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $email = $facebookUser->getEmail() ?? $facebookUser->getId() . '@facebook.local';

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $facebookUser->getName() ?? 'Utilisateur Facebook',
                    'facebook_id' => $facebookUser->getId(),
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            Auth::login($user);

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/dashboard');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Erreur Facebook Login : ' . $e->getMessage());
        }
    }
}
