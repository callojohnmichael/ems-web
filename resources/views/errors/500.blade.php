@include('errors.partials.page', [
    'status' => 500,
    'icon' => 'server',
    'title' => 'Server Error',
    'message' => 'Something went wrong on our side. Please return home and try again in a few minutes.',
])
