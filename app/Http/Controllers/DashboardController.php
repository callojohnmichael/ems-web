<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Redirect to the role-specific dashboard.
     */
    public function redirect(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return redirect()->route($user->dashboardRoute());
    }

    public function admin(): View
    {
        return view('dashboard.admin');
    }

    public function user(): View
    {
        return view('dashboard.user');
    }

    public function media(): View
    {
        return view('dashboard.media');
    }

    /**
     * Role test pages (to verify 403 when accessed by wrong role).
     */
    public function adminApprovals(): View
    {
        return view('test.admin-approvals');
    }

    public function userRequests(): View
    {
        return view('test.user-requests');
    }

    public function mediaPosts(): View
    {
        return view('test.media-posts');
    }
}
