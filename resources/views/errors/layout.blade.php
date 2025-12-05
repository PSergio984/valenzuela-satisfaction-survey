<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title') - {{ config('app.name', 'Valenzuela Survey') }}</title>

        <!-- Styles -->
        <style>
            *, *::before, *::after {
                box-sizing: border-box;
            }
            html, body {
                background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
                color: #1f2937;
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                font-weight: 400;
                min-height: 100vh;
                margin: 0;
            }

            .container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 2rem;
            }

            .content {
                text-align: center;
                max-width: 500px;
            }

            .error-code {
                font-size: 8rem;
                font-weight: 700;
                color: #3b82f6;
                line-height: 1;
                margin-bottom: 1rem;
            }

            .error-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 0.5rem;
            }

            .error-message {
                font-size: 1rem;
                color: #6b7280;
                margin-bottom: 2rem;
            }

            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                font-size: 0.875rem;
                font-weight: 500;
                color: #ffffff;
                background-color: #3b82f6;
                border: none;
                border-radius: 0.5rem;
                text-decoration: none;
                transition: background-color 0.2s;
            }

            .btn:hover {
                background-color: #2563eb;
            }

            .logo {
                margin-bottom: 2rem;
            }

            .logo-icon {
                width: 4rem;
                height: 4rem;
                background-color: #3b82f6;
                border-radius: 0.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
            }

            .logo-icon svg {
                width: 2rem;
                height: 2rem;
                fill: white;
            }

            @media (prefers-color-scheme: dark) {
                html, body {
                    background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
                    color: #f9fafb;
                }
                .error-title {
                    color: #f9fafb;
                }
                .error-message {
                    color: #9ca3af;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="logo">
                    <div class="logo-icon">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 2C5.34315 2 4 3.34315 4 5V19C4 20.6569 5.34315 22 7 22H17C18.6569 22 20 20.6569 20 19V5C20 3.34315 18.6569 2 17 2H7ZM8 6C7.44772 6 7 6.44772 7 7C7 7.55228 7.44772 8 8 8H16C16.5523 8 17 7.55228 17 7C17 6.44772 16.5523 6 16 6H8ZM7 11C7 10.4477 7.44772 10 8 10H16C16.5523 10 17 10.4477 17 11C17 11.5523 16.5523 12 16 12H8C7.44772 12 7 11.5523 7 11ZM8 14C7.44772 14 7 14.4477 7 15C7 15.5523 7.44772 16 8 16H12C12.5523 16 13 15.5523 13 15C13 14.4477 12.5523 14 12 14H8Z"/>
                        </svg>
                    </div>
                </div>
                <div class="error-code">@yield('code')</div>
                <div class="error-title">@yield('title')</div>
                <div class="error-message">@yield('message')</div>
                <a href="{{ url('/') }}" class="btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Go Home
                </a>
            </div>
        </div>
    </body>
</html>
