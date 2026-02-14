<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status }} - {{ $title }}</title>
    <style>
        :root {
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f1f5f9;
            color: #334155;
            font-family: "Inter", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 700px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            padding: 42px 28px;
            text-align: center;
        }

        .icon {
            width: 96px;
            height: 96px;
            margin: 0 auto 20px;
            border-radius: 999px;
            border: 4px solid #cbd5e1;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status {
            margin: 0;
            font-size: 54px;
            line-height: 1;
            color: #64748b;
            font-weight: 700;
        }

        .title {
            margin: 14px 0 0;
            font-size: 34px;
            font-weight: 600;
            color: #334155;
        }

        .message {
            margin: 16px auto 0;
            max-width: 580px;
            font-size: 17px;
            line-height: 1.6;
            color: #64748b;
        }

        .action {
            margin-top: 30px;
        }

        .button {
            display: inline-block;
            border-radius: 10px;
            background: #334155;
            color: #fff;
            padding: 12px 22px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.18s ease;
        }

        .button:hover {
            background: #1e293b;
        }

        .button:focus-visible {
            outline: 2px solid #94a3b8;
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    @php($icon = $icon ?? 'alert')
    <main class="wrapper">
        <section class="card">
            <div class="icon">
                <svg width="46" height="46" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    @if ($icon === 'lock')
                        <rect x="6" y="11" width="12" height="9" rx="2"></rect>
                        <path d="M9 11V8a3 3 0 0 1 6 0v3"></path>
                    @elseif ($icon === 'shield')
                        <path d="M12 3l7 3v6c0 4.3-2.8 6.9-7 9-4.2-2.1-7-4.7-7-9V6l7-3z"></path>
                        <path d="M9.5 12.5l1.8 1.8 3.2-3.6"></path>
                    @elseif ($icon === 'search')
                        <circle cx="11" cy="11" r="6"></circle>
                        <path d="M20 20l-4.2-4.2"></path>
                        <path d="M8.7 11h4.6"></path>
                    @elseif ($icon === 'clock')
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 7v5l3 2"></path>
                    @elseif ($icon === 'warning')
                        <path d="M12 4l8 14H4l8-14z"></path>
                        <path d="M12 9v4"></path>
                        <circle cx="12" cy="16" r="0.8"></circle>
                    @elseif ($icon === 'server')
                        <rect x="4" y="5" width="16" height="6" rx="1.5"></rect>
                        <rect x="4" y="13" width="16" height="6" rx="1.5"></rect>
                        <path d="M7 8h.01M7 16h.01"></path>
                    @elseif ($icon === 'tools')
                        <path d="M5 20l6-6"></path>
                        <path d="M14.5 8.5l3-3a3 3 0 0 1-4 4l-3 3"></path>
                        <path d="M9.5 9.5l5 5"></path>
                    @else
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v5"></path>
                        <circle cx="12" cy="16.5" r="0.8"></circle>
                    @endif
                </svg>
            </div>

            <p class="status">{{ $status }}</p>
            <h1 class="title">{{ $title }}</h1>
            <p class="message">{{ $message }}</p>

            <div class="action">
                <a href="{{ route('dashboard') }}" class="button">
                    Back to Home
                </a>
            </div>
        </section>
    </main>
</body>
</html>
