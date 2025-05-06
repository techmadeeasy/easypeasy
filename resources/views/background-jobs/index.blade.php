<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Background Jobs Dashboard</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
            @layer theme{
                :root,
                :host{
                    /* custom properties */
                    --font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
                    --default-font-family:var(--font-sans);
                    --default-font-feature-settings:normal;
                    --default-font-variation-settings:normal;
                    --default-mono-font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
                    --default-mono-font-feature-settings:normal;
                    --default-mono-font-variation-settings:normal;
                    /* ... rest of tailwind styles ... */
                }
            }
            /* rest of styles... */
        </style>
    @endif
</head>
<body class="bg-[#FDFDFC] text-[#1b1b18] p-6 lg:p-8 min-h-screen">
<header class="mb-8 text-center">
    <h1 class="text-3xl font-bold mb-2">Background Jobs Dashboard</h1>
    <p class="text-lg text-gray-600">View and manage your background jobs. You can cancel jobs that are currently queued.</p>
</header>

<div class="flex flex-col items-center justify-center">
    <livewire:background-job></livewire:background-job>
</div>

@if (Route::has('login'))
    <div class="h-14.5 hidden lg:block"></div>
@endif
</body>
</html>
