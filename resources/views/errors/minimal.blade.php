<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }} - @yield('title')</title>

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
                background-color: #f3f4f6;
                color: #1f2937;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 2rem;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .container {
                text-align: center;
                max-width: 32rem;
            }

            .brand {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                margin-bottom: 3rem;
            }

            .brand-icon {
                width: 2.5rem;
                height: 2.5rem;
                color: #2563eb;
            }

            .brand-name {
                font-size: 1.25rem;
                font-weight: 600;
                color: #1f2937;
            }

            .error-code {
                font-size: 6rem;
                font-weight: 700;
                color: #2563eb;
                line-height: 1;
                margin-bottom: 1rem;
            }

            .error-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 1rem;
            }

            .error-message {
                font-size: 1rem;
                color: #6b7280;
                margin-bottom: 2rem;
                line-height: 1.6;
            }

            .home-link {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                background-color: #2563eb;
                color: white;
                text-decoration: none;
                border-radius: 0.5rem;
                font-weight: 500;
                transition: background-color 0.2s;
            }

            .home-link:hover {
                background-color: #1d4ed8;
            }

            @media (prefers-color-scheme: dark) {
                body {
                    background-color: #111827;
                    color: #f9fafb;
                }

                .brand-name {
                    color: #f9fafb;
                }

                .error-code {
                    color: #3b82f6;
                }

                .error-title {
                    color: #e5e7eb;
                }

                .error-message {
                    color: #9ca3af;
                }
            }
        </style>
    </head>
    <body>
        <div class="container" role="main">
            <div class="brand">
                <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
                <span class="brand-name">{{ config('app.name') }}</span>
            </div>

            <div class="error-code">@yield('code')</div>
            <h1 class="error-title">@yield('title')</h1>
            <p class="error-message">@yield('message')</p>

            <a href="/" class="home-link">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Go Home
            </a>
        </div>
    </body>
</html>
