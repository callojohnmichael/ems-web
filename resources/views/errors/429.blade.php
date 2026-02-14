@include('errors.partials.page', [
    'status' => 429,
    'icon' => 'warning',
    'title' => 'Too Many Requests',
    'message' => 'You have made too many requests in a short time. Please wait a moment and try again.',
])
