@include('errors.partials.page', [
    'status' => 401,
    'icon' => 'lock',
    'title' => 'Unauthorized',
    'message' => 'You are not authorized to view this page. Please sign in with the correct account and try again.',
])
