<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRAIL Register</title>
    <!-- Google Fonts: Anton -->
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            min-width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .font-anton { font-family: 'Anton', sans-serif; }
        .absolute { position: absolute; }
        .relative { position: relative; }
        .z-0 { z-index: 0; }
        .z-10 { z-index: 10; }
        .w-full { width: 100%; }
        .h-full { height: 100%; }
        .object-cover { object-fit: cover; }
        .blur-sm { filter: blur(6px); }
        .scale-105 { transform: scale(1.05); }
        .bg-white\/80 { background: rgba(255,229,229,0.8); } /* light red overlay */
        .bg-white { background: #ffe5e5; } /* very light red */
        .bg-white\/90 { background: rgba(255,229,229,0.9); } /* lighter red for inputs */
        .max-w-md { max-width: 28rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .skew-y-\[-6deg\] { transform: skewY(-6deg); background: #ffe5e5; }
        .skew-y-\[6deg\] { transform: skewY(6deg); }
        .shadow-2xl { box-shadow: 0 10px 25px rgba(179,7,7,0.15); }
        .rounded-2xl { border-radius: 1rem; }
        .p-6 { padding: 1.5rem; }
        .sm\:p-8 { padding: 2rem; }
        .text-center { text-align: center; }
        .mb-6 { margin-bottom: 1.5rem; }
        .text-4xl { font-size: 2.25rem; }
        .text-[#d30707] { color: #d30707; }
        .mb-1 { margin-bottom: 0.25rem; }
        .tracking-wide { letter-spacing: 0.05em; }
        .text-base { font-size: 1rem; }
        .font-semibold { font-weight: 600; }
        .text-gray-700 { color: #991b1b; }
        .mb-2 { margin-bottom: 0.5rem; }
        .p-4 { padding: 1rem; }
        .bg-red-50 { background: #fef2f2; }
        .border { border-width: 1px; border-style: solid; }
        .border-red-200 { border-color: #ffcccc; }
        .rounded-lg { border-radius: 0.5rem; }
        .flex { display: flex; }
        .h-5 { height: 1.25rem; }
        .w-5 { width: 1.25rem; }
        .text-red-400 { color: #d30707; }
        .mr-2 { margin-right: 0.5rem; }
        .text-sm { font-size: 0.875rem; }
        .text-red-800 { color: #991b1b; }
        .space-y-6 > :not([hidden]) ~ :not([hidden]) { margin-top: 1.5rem; }
        .block { display: block; }
        .font-medium { font-weight: 500; }
        .w-full { width: 100%; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .border-gray-300 { border-color: #ffcccc; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px #d30707; }
        .focus\:ring-\[#d30707\]:focus { box-shadow: 0 0 0 2px #d30707; }
        .focus\:border-\[#d30707\]:focus { border-color: #d30707; }
        .transition-colors { transition: color 0.2s, background 0.2s, border-color 0.2s; }
        .duration-200 { transition-duration: 0.2s; }
        .relative { position: relative; }
        .pr-12 { padding-right: 3rem; }
        .absolute { position: absolute; }
        .inset-y-0 { top: 0; bottom: 0; }
        .right-0 { right: 0; }
        .pr-3 { padding-right: 0.75rem; }
        .items-center { align-items: center; }
        .text-gray-400 { color: #d30707; }
        .hover\:text-gray-600:hover { color: #b70707; }
        .transition-colors { transition: color 0.2s; }
        .bg-\[#d30707\] { background: #d30707; }
        .hover\:bg-\[#b70707\]:hover { background: #b70707; }
        .text-white { color: #fff; }
        .font-bold { font-weight: 700; }
        .focus\:outline-none:focus { outline: none; }
        .focus\:ring-2:focus { box-shadow: 0 0 0 2px #d30707; }
        .focus\:ring-\[#d30707\]:focus { box-shadow: 0 0 0 2px #d30707; }
        .focus\:ring-offset-2:focus { box-shadow: 0 0 0 4px #ffe5e5, 0 0 0 6px #d30707; }
        .text-lg { font-size: 1.125rem; }
        .tracking-wide { letter-spacing: 0.05em; }
        .mt-6 { margin-top: 1.5rem; }
        .text-gray-600 { color: #b70707; }
        .font-semibold { font-weight: 600; }
        .transition-colors { transition: color 0.2s; }
        .duration-200 { transition-duration: 0.2s; }
        .hidden { display: none; }
        .min-h-screen { min-height: 100vh; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .background-image-fullscreen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
        }
        .background-image-fullscreen img {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
            display: block;
        }
        .background-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255,229,229,0.8); /* light red overlay */
            z-index: 1;
        }
        .form-wrapper {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
            padding: 0 1rem;
        }
        .btn-red {
            background: #d30707;
            color: #fff;
            font-weight: 700;
            border: none;
            box-shadow: 0 2px 8px rgba(179,7,7,0.10);
            transition: background 0.2s, color 0.2s;
        }
        .btn-red:hover, .btn-red:focus {
            background: #991b1b;
            color: #fff;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Blurred Background Image with Overlay -->
    <div class="background-image-fullscreen">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80" alt="University Building" class="" />
        <div class="background-overlay"></div>
    </div>
    <!-- Skewed Registration Panel -->
    <div class="relative z-10 flex items-center justify-center min-h-screen">
        <div class="skew-y-[-6deg] bg-white shadow-2xl rounded-2xl p-6 sm:p-8">
            <div class="skew-y-[6deg] form-wrapper">
                <div class="text-center mb-6">
                    <h1 class="text-4xl font-anton mb-1 tracking-wide" style="color: #d30707;">GRAIL</h1>
                    <p class="text-base font-semibold text-gray-700 mb-2 tracking-wide">Grade and Risk Assessment through Intelligent Learning</p>
                    <p class="text-gray-700 text-base">Create your account</p>
                </div>
                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <ul class="text-sm text-red-800 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <form method="POST" action="{{ route('register') }}" class="space-y-6 w-full">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full name</label>
                        <input id="name" type="text" name="name" required autofocus value="{{ old('name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d30707] focus:border-[#d30707] transition-colors duration-200 bg-white/90" placeholder="Enter your full name">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                        <input id="email" type="email" name="email" required value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d30707] focus:border-[#d30707] transition-colors duration-200 bg-white/90" placeholder="Enter your email">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d30707] focus:border-[#d30707] transition-colors duration-200 bg-white/90" placeholder="Create a password">
                            <button type="button" onclick="togglePassword('password', 'eye1', 'eye-off1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors" tabindex="-1">
                                <!-- Eye SVG (visible by default) -->
                                <svg id="eye1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye-off SVG (hidden by default) -->
                                <svg id="eye-off1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.956 9.956 0 012.293-3.95M6.634 6.634A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.956 9.956 0 01-4.422 5.568M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters</p>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#d30707] focus:border-[#d30707] transition-colors duration-200 bg-white/90" placeholder="Confirm your password">
                            <button type="button" onclick="togglePassword('password_confirmation', 'eye2', 'eye-off2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors" tabindex="-1">
                                <!-- Eye SVG (visible by default) -->
                                <svg id="eye2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye-off SVG (hidden by default) -->
                                <svg id="eye-off2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.956 9.956 0 012.293-3.95M6.634 6.634A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.956 9.956 0 01-4.422 5.568M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="w-full btn-red rounded-lg py-3 px-4 focus:outline-none focus:ring-2 focus:ring-[#d30707] focus:ring-offset-2 font-anton text-lg tracking-wide">Create account</button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-[#d30707] hover:text-[#b70707] transition-colors duration-200">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword(inputId, eyeId, eyeOffId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeOff = document.getElementById(eyeOffId);
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.add('hidden');
                eyeOff.classList.remove('hidden');
            } else {
                input.type = 'password';
                eye.classList.remove('hidden');
                eyeOff.classList.add('hidden');
            }
        }
    </script>
</body>
</html> 