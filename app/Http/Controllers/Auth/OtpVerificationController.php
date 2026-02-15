<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    private const OTP_TTL_SECONDS = 60;

    private const OTP_LENGTH = 6;

    private const MAX_ATTEMPTS = 5;

    private const ATTEMPT_DECAY_SECONDS = 900; // 15 minutes

    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $expiresAt = (int) $request->session()->get('otp_expires_at', now()->timestamp);
        $secondsRemaining = max(0, $expiresAt - now()->timestamp);

        return view('auth.verify-otp', [
            'otpSecondsRemaining' => $secondsRemaining,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'string', 'size:6']]);

        $userId = $this->resolvePendingUserId($request);
        if (! $userId) {
            return redirect()->route('login');
        }

        $attemptKey = 'otp_attempts:'.$userId;
        $attempts = (int) Cache::get($attemptKey, 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            $request->session()->forget('otp_user_id');
            Cache::forget($attemptKey);
            throw ValidationException::withMessages([
                'otp' => [__('Too many failed attempts. Please log in again.')],
            ]);
        }

        $cacheKey = 'otp:user:'.$userId;
        $storedOtp = Cache::get($cacheKey);
        if ($storedOtp === null || ! hash_equals((string) $storedOtp, (string) $request->input('otp'))) {
            Cache::put($attemptKey, $attempts + 1, self::ATTEMPT_DECAY_SECONDS);
            throw ValidationException::withMessages([
                'otp' => [__('The verification code is invalid or has expired.')],
            ]);
        }

        Cache::forget($cacheKey);
        Cache::forget($attemptKey);
        $request->session()->forget('otp_user_id');

        $user = User::findOrFail($userId);
        Auth::guard('web')->login($user, false);
        $request->session()->regenerate();
        $request->session()->flash('success', __('Login Successful! Welcome to the School Event Management System.'));

        return redirect()->intended(route($user->dashboardRoute(), absolute: false));
    }

    public function resend(Request $request): RedirectResponse
    {
        $userId = $this->resolvePendingUserId($request);
        if (! $userId) {
            return redirect()->route('login');
        }

        $expiresAt = (int) $request->session()->get('otp_expires_at', now()->timestamp);
        if ($expiresAt > now()->timestamp) {
            $secondsRemaining = max(1, $expiresAt - now()->timestamp);
            throw ValidationException::withMessages([
                'otp' => [__('Please wait :seconds seconds before requesting a new code.', ['seconds' => $secondsRemaining])],
            ]);
        }

        $user = User::findOrFail($userId);
        $otp = $this->generateOtp();
        Cache::put('otp:user:'.$userId, $otp, self::OTP_TTL_SECONDS);
        $request->session()->put('otp_expires_at', now()->addSeconds(self::OTP_TTL_SECONDS)->timestamp);

        $user->notify(new OtpVerificationNotification($otp));

        return back()->with('status', __('A new verification code has been sent to your email.'));
    }

    private function resolvePendingUserId(Request $request): ?int
    {
        $encrypted = $request->session()->get('otp_user_id');
        if (! $encrypted) {
            return null;
        }

        try {
            return (int) decrypt($encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    private function generateOtp(): string
    {
        $digits = '';
        for ($i = 0; $i < self::OTP_LENGTH; $i++) {
            $digits .= (string) random_int(0, 9);
        }

        return $digits;
    }
}
