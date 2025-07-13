<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <title>GRAIL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
      tailwind.config = {
        darkMode: 'class',
        theme: {
          extend: {
            colors: {
              evsu: '#d30707',
              evsuDark: '#b70707',
            },
          },
        },
      }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100 relative">
    <!-- Sidebar -->
    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 border-r shadow-lg z-40 transform -translate-x-full transition-transform duration-300 ease-in-out">
      <!-- Close Button -->
      <div class="flex justify-between items-center px-4 py-3 border-b dark:border-gray-700">
        <h2 class="text-lg font-bold text-red-700 dark:text-evsu">Menu</h2>
        <button id="closeSidebar" class="text-gray-600 dark:text-gray-200 hover:text-red-600 dark:hover:text-evsu">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>
      <!-- Navigation -->
      <nav class="mt-4 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 py-3 px-4 rounded-md text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
          <i data-lucide="layout-dashboard" class="w-5 h-5 text-red-600 dark:text-evsu"></i>
          <span class="font-medium">Dashboard</span>
        </a>
        <a href="{{ route('subjects.index') }}" class="flex items-center space-x-3 py-3 px-4 rounded-md text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
          <i data-lucide="book" class="w-5 h-5 text-red-600 dark:text-evsu"></i>
          <span class="font-medium">Subjects</span>
        </a>
        <a href="{{ route('students.index') }}" class="flex items-center space-x-3 py-3 px-4 rounded-md text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
          <i data-lucide="users" class="w-5 h-5 text-red-600 dark:text-evsu"></i>
          <span class="font-medium">Students</span>
        </a>
        <button onclick="confirmLogout()" class="flex items-center space-x-3 py-3 px-4 rounded-md text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 transition w-full text-left">
            <i data-lucide="log-out" class="w-5 h-5 text-red-600 dark:text-evsu"></i>
            <span class="font-medium">Logout</span>
        </button>
        <form id="logoutForm" method="POST" action="{{ url('/logout') }}" class="hidden">
            @csrf
        </form>
      </nav>
    </div>
    <!-- Top Bar -->
    <header class="flex items-center justify-between bg-white dark:bg-gray-800 px-6 py-4 shadow-md sticky top-0 z-30">
      <button id="toggleSidebar" class="text-gray-700 dark:text-gray-100 hover:text-red-600 dark:hover:text-evsu focus:outline-none">
        <i data-lucide="menu" class="w-6 h-6"></i>
      </button>
      <h1 class="text-xl font-bold tracking-wide text-red-700 dark:text-evsu">GRAIL</h1>
      <button id="darkModeToggle" class="ml-4 text-gray-700 dark:text-gray-100 hover:text-red-600 dark:hover:text-evsu focus:outline-none" aria-label="Toggle dark mode" onclick="toggleDarkMode()">
        <i id="darkModeIcon" data-lucide="sun" class="w-6 h-6"></i>
      </button>
    </header>
    <!-- Main Content -->
    <main class="p-6">
      @yield('content')
    </main>
    <!-- Scripts -->
    <script>
      const sidebar = document.getElementById('sidebar');
      const toggleBtn = document.getElementById('toggleSidebar');
      const closeBtn = document.getElementById('closeSidebar');
      const html = document.documentElement;
      
      // Sidebar toggle
      toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
      });
      closeBtn.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
      });
      
      // Simple dark mode toggle function
      function toggleDarkMode() {
        const darkModeIcon = document.getElementById('darkModeIcon');
        const isDark = html.classList.contains('dark');
        
        if (isDark) {
          // Switch to light mode
          html.classList.remove('dark');
          darkModeIcon.setAttribute('data-lucide', 'moon');
          localStorage.setItem('grail-darkmode', '0');
        } else {
          // Switch to dark mode
          html.classList.add('dark');
          darkModeIcon.setAttribute('data-lucide', 'sun');
          localStorage.setItem('grail-darkmode', '1');
        }
        
        lucide.createIcons();
      }
      
      // Initial dark mode state
      const darkPref = localStorage.getItem('grail-darkmode');
      if (darkPref === '1' || (darkPref === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        html.classList.add('dark');
        document.getElementById('darkModeIcon').setAttribute('data-lucide', 'sun');
      } else {
        html.classList.remove('dark');
        document.getElementById('darkModeIcon').setAttribute('data-lucide', 'moon');
      }
      lucide.createIcons();

      function confirmLogout() {
        if (confirm('Are you sure you want to log out?')) {
          document.getElementById('logoutForm').submit();
        }
      }
    </script>
</body>
</html> 