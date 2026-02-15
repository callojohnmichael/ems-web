<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCustodianRequest;
use App\Models\EventFinanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustodianFinanceApprovalController extends Controller
{
    public function index(): View
    {
        $pendingFinance = EventFinanceRequest::with('event')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCustodian = EventCustodianRequest::with(['event', 'custodianMaterial'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.custodian-finance-approvals.index', [
            'pendingFinance' => $pendingFinance,
            'pendingCustodian' => $pendingCustodian,
        ]);
    }

    public function updateFinanceStatus(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $financeRequest = $event->financeRequest;
        if (!$financeRequest) {
            return back()->withErrors(['finance' => 'No finance request found for this event.']);
        }

        $financeRequest->update(['status' => $request->input('status')]);

        $action = $request->input('status') === 'approved' ? 'approved' : 'rejected';
        return back()->with('success', "Finance request {$action}.");
    }

    public function updateCustodianStatus(Request $request, EventCustodianRequest $custodianRequest): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $custodianRequest->update(['status' => $request->input('status')]);

        $action = $request->input('status') === 'approved' ? 'approved' : 'rejected';
        return back()->with('success', "Custodian request {$action}.");
    }
}
