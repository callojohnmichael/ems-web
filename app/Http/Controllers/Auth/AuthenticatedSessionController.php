<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private const OTP_TTL_SECONDS = 60;

    private const OTP_LENGTH = 6;

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request (password only; then 2FA OTP).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();
        $userId = $user->id;

        $request->session()->put('otp_user_id', encrypt($userId));
        $request->session()->put('otp_remember', $request->boolean('remember'));

        $otp = $this->generateOtp();
        Cache::put('otp:user:'.$userId, $otp, self::OTP_TTL_SECONDS);

        $user->notify(new OtpVerificationNotification($otp));

        Auth::guard('web')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('otp.verify');
    }

    private function generateOtp(): string
    {
        $digits = '';
        for ($i = 0; $i < self::OTP_LENGTH; $i++) {
            $digits .= (string) random_int(0, 9);
        }

        return $digits;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
