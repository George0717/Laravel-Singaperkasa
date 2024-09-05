<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Home')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        /* Sidebar animation and styling */
        .sidebar {
            transition: transform 0.3s ease;
            transform: translateX(-100%);
            width: 250px;
            height: 100vh;
            background-color: #1f2937;
            /* Tailwind dark-gray-800 */
            position: fixed;
            top: 0;
            left: 0;
            z-index: 40;
        }

        .sidebar-open .sidebar {
            transform: translateX(0);
        }

        .overlay {
            transition: opacity 0.3s ease;
            opacity: 0;
            pointer-events: none;
        }

        .overlay-open .overlay {
            opacity: 0.5;
            pointer-events: auto;
        }

        .sidebar a {
            display: block;
            padding: 1rem;
            color: #e5e7eb;
            /* Tailwind gray-200 */
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #374151;
            /* Tailwind gray-700 */
            color: #ffffff;
        }

        #sidebar-toggle:active {
            opacity: 1;
        }

        /* Button primary styling */
        .btn-primary {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            /* Rounded corners */
            background-color: #158843;
            /* Your primary color */
            color: #ffffff;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        /* Button primary hover effect */
        .btn-primary:hover {
            background-color: #1e9e5f;
            /* Slightly lighter shade for hover effect */
            transform: scale(1.05);
            /* Slight zoom effect on hover */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Shadow effect */
        }

        /* Button primary focus effect */
        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.5);
            /* Focus ring */
        }

        .skeleton {
            background-color: #e0e0e0;
            position: relative;
            overflow: hidden;
        }

        .skeleton::before {
            content: '';
            display: block;
            position: absolute;
            top: 0;
            left: -150%;
            width: 200%;
            height: 100%;
            background-image: linear-gradient(90deg,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0.8) 50%,
                    rgba(255, 255, 255, 0) 100%);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            100% {
                transform: translateX(150%);
            }
        }

        /* Add this CSS to your stylesheet or inside a <style> block in your Blade template */

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 1s ease-out;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    :class="{ 'sidebar-open': sidebarOpen }" x-data="{ sidebarOpen: false }"
    x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebar-open', value))">

    <!-- Sidebar Component -->
    @include('components.app.sidebar')

    <!-- Overlay -->
    <div id="overlay" class="overlay fixed inset-0 bg-black"></div>

    <!-- Page wrapper -->
    <div class="flex h-[100dvh] overflow-hidden">

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden {{ $attributes['background'] ?? '' }}"
            x-ref="contentarea">

            <!-- Header component -->
            <x-app.header :variant="$attributes['headerVariant'] ?? 'default'" />

            <div class="container mx-auto px-4 py-6">
                @yield('content')
            </div>

        </div>

    </div>

    @livewireScriptConfig

    <!-- Sidebar Toggle Script -->
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
            document.body.classList.toggle('overlay-open');
        });

        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function () {
                document.querySelectorAll('.sidebar a').forEach(link => link.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>