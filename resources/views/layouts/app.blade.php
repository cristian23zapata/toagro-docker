<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
        <!-- Global iframe modal for showing pages inside a modal.
             This modal is hidden by default and can be triggered from
             any page using the openIframeModal() function defined below. -->
        <div id="generic-iframe-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl h-[90vh] overflow-y-auto relative">
                <button type="button" onclick="closeIframeModal()" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl leading-none">
                    &times;
                </button>
                <iframe id="generic-iframe" class="w-full h-full" src="" frameborder="0"></iframe>
            </div>
        </div>
    </div>

    {{-- Modal handling scripts available globally --}}
    <script>
        function openIframeModal(url) {
            var modal = document.getElementById('generic-iframe-modal');
            var iframe = document.getElementById('generic-iframe');
            if (iframe) {
                iframe.src = url;
            }
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeIframeModal() {
            var modal = document.getElementById('generic-iframe-modal');
            var iframe = document.getElementById('generic-iframe');
            if (iframe) {
                iframe.src = '';
            }
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>

    @yield('scripts')

    {{-- Auto-close modal and refresh parent page on success messages --}}
    @if (session('success'))
    <script>
        // When this page is loaded inside an iframe (i.e., within our generic modal),
        // close the iframe modal and refresh the parent window to reflect any changes.
        if (window.self !== window.top && window.parent && typeof window.parent.closeIframeModal === 'function') {
            window.parent.closeIframeModal();
            // Refresh the parent page so newly created or updated records are visible.
            window.parent.location.reload();
        }
    </script>
    @endif
</body>

</html>
