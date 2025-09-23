<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function show()
    {
        return view('auth.otp'); // formulaire pour entrer le code
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect('/login')->withErrors(['code' => 'Session expirée, reconnectez-vous.']);
        }

        $otp = EmailVerification::where('user_id', $userId)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'Code invalide ou expiré.']);
        }

        // Code correct → connecter l’utilisateur
        $user = User::find($userId);
        Auth::login($user);

        $otp->delete();
        session()->forget('otp_user_id');

        return redirect('/dashboard');
    }
}

