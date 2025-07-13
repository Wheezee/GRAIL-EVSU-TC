<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Laravel</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-purple-50 to-pink-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Laravel App</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                    <button onclick="confirmLogout()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                        Logout
                    </button>
                    <form id="logoutForm" method="POST" action="{{ url('/logout') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Welcome Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-4">
                    <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                <p class="text-gray-600 mb-6">You're successfully logged into your account.</p>
                <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    Account Verified
                </div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-lg text-gray-900">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-lg text-gray-900">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Member Since</label>
                        <p class="mt-1 text-lg text-gray-900">{{ auth()->user()->created_at->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Login</label>
                        <p class="mt-1 text-lg text-gray-900">{{ now()->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                document.getElementById('logoutForm').submit();
            }
        }
    </script>
</body>
</html> 