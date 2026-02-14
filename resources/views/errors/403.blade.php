@include('errors.partials.page', [
    'status' => 403,
    'icon' => 'shield',
    'title' => 'Access Denied',
    'message' => 'You do not have permission to access this page. If you think this is a mistake, please contact support.',
])
