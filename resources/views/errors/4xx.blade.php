@include('errors.partials.page', [
    'status' => 400,
    'icon' => 'warning',
    'title' => 'Request Error',
    'message' => 'There was a problem with this request. Please go back home and try again.',
])
