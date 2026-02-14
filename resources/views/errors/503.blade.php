@include('errors.partials.page', [
    'status' => 503,
    'icon' => 'tools',
    'title' => 'Service Unavailable',
    'message' => 'The service is temporarily unavailable due to maintenance or heavy traffic. Please try again shortly.',
])
