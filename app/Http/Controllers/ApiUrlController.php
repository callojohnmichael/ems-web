<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiUrlController extends Controller
{
    /**
     * Get the base API URL for JavaScript
     */
    public function getApiUrl()
    {
        return response()->json([
            'api_url' => url('/api'),
            'base_url' => url('/'),
            'app_url' => config('app.url')
        ]);
    }
}
