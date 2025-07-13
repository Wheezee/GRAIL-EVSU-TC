<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRAIL Admin Dashboard</title>
    <!-- Google Fonts: Anton -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-anton { font-family: 'Anton', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" id="sidebar">
            <!-- Profile Section -->
            <div class="flex items-center justify-center h-20 bg-[#d30707]">
                <h1 class="text-2xl font-anton text-white tracking-wide">GRAIL</h1>
            </div>
            
            <!-- Profile Info -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-[#d30707] rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    <a href="#" class="flex items-center px-4 py-3 text-[#d30707] bg-red-50 border-r-4 border-[#d30707] rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:text-[#d30707] hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Student Management
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:text-[#d30707] hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Grade Analytics
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:text-[#d30707] hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Risk Assessment
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-gray-600 hover:text-[#d30707] hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        System Settings
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-64">
            <!-- Top Navigation -->
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center">
                        <button class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100" onclick="toggleSidebar()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-900 ml-4 lg:ml-0">Admin Dashboard</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="confirmLogout()" class="bg-[#d30707] hover:bg-[#b70707] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            Logout
                        </button>
                        <form id="logoutForm" method="POST" action="{{ url('/logout') }}" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="p-6">
                <!-- Welcome Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-anton text-[#d30707] mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
                    <p class="text-gray-600">Here's what's happening with your GRAIL system today.</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Students -->
                    <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Total Students</p>
                                <p class="text-2xl font-bold text-red-900">1,247</p>
                            </div>
                        </div>
                    </div>

                    <!-- Active Courses -->
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Active Courses</p>
                                <p class="text-2xl font-bold text-blue-900">89</p>
                            </div>
                        </div>
                    </div>

                    <!-- Risk Alerts -->
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">Risk Alerts</p>
                                <p class="text-2xl font-bold text-yellow-900">23</p>
                            </div>
                        </div>
                    </div>

                    <!-- System Health -->
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">System Health</p>
                                <p class="text-2xl font-bold text-green-900">98%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">New student registration: John Doe</p>
                                <p class="text-xs text-gray-500">2 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Risk assessment triggered for Student ID: 12345</p>
                                <p class="text-xs text-gray-500">15 minutes ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Grade update completed for Course: CS101</p>
                                <p class="text-xs text-gray-500">1 hour ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function confirmLogout() {
            if (confirm('Are you sure you want to log out?')) {
                document.getElementById('logoutForm').submit();
            }
        }
    </script>
</body>
</html> 