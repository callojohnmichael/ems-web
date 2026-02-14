@include('errors.partials.page', [
    'status' => 500,
    'icon' => 'server',
    'title' => 'Temporary Problem',
    'message' => 'We are having trouble loading this page right now. Please return home and try again shortly.',
])
