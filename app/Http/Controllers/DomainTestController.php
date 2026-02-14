<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DomainTestController extends Controller
{
    public function test()
    {
        return response()->json([
            'app_url' => config('app.url'),
            'base_url' => url('/'),
            'api_url' => url('/api'),
            'events_url' => url('/api/events'),
            'current_url' => url()->current(),
            'request_host' => request()->getHost(),
            'request_scheme' => request()->getScheme(),
        ]);
    }
}
