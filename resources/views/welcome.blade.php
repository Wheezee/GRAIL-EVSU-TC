<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRAIL Login</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            min-width: 100vw;
            background: #faf7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-container {
            background: #fff;
            border: 1px solid #f3caca;
            border-radius: 1.5vw;
            box-shadow: 0 2px 12px rgba(179,7,7,0.06);
            width: 90vw;
            max-width: 350px;
            min-width: 250px;
            padding: 4vw 2vw 4vw 2vw;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-title {
            color: #b70707;
            font-size: 2.2vw;
            min-font-size: 1.2rem;
            margin-bottom: 0.5vw;
            text-align: center;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .login-subtitle {
            color: #991b1b;
            font-size: 1.2vw;
            min-font-size: 1rem;
            text-align: center;
            margin-bottom: 2vw;
        }
        .form-label {
            display: block;
            font-size: 1vw;
            min-font-size: 0.97rem;
            color: #991b1b;
            margin-bottom: 0.5vw;
            font-weight: 500;
        }
        form {
            width: 100%;
        }
        .form-input {
            width: 90%;
            display: block;
            margin-left: auto;
            margin-right: auto;
            padding: 0.7vw 1vw;
            border: 1px solid #f3caca;
            border-radius: 0.7vw;
            background: #fff;
            font-size: 1vw;
            min-font-size: 1rem;
            margin-bottom: 1.2vw;
            transition: border 0.2s;
            box-sizing: border-box;
        }
        .form-input:focus {
            border: 1.5px solid #d30707;
            outline: none;
        }
        .form-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #ffcccc;
            border-radius: 0.7vw;
            padding: 0.7vw 1vw;
            font-size: 1vw;
            min-font-size: 0.95rem;
            margin-bottom: 1.2vw;
            width: 100%;
            box-sizing: border-box;
        }
        .login-btn {
            width: 100%;
            background: #d30707;
            color: #fff;
            border: none;
            border-radius: 0.7vw;
            padding: 0.9vw 0;
            font-size: 1.1vw;
            min-font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.18s;
            margin-top: 0.2vw;
        }
        .login-btn:hover, .login-btn:focus {
            background: #991b1b;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 1.5vw;
            font-size: 1vw;
            min-font-size: 0.97rem;
            color: #991b1b;
        }
        .login-link a {
            color: #d30707;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.18s;
        }
        .login-link a:hover {
            color: #991b1b;
        }
        @media (max-width: 500px) {
            .login-container {
                max-width: 98vw;
                padding: 6vw 3vw;
            }
            .login-title, .login-subtitle, .form-label, .form-input, .form-error, .login-btn, .login-link {
                font-size: 1rem !important;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-title">GRAIL</div>
        <div class="login-subtitle">Sign in to your account</div>
        @if(session('error'))
            <div class="form-error">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}" style="width:100%">
            @csrf
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" required autofocus class="form-input" placeholder="Enter your email">

            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" required class="form-input" placeholder="Enter your password">

            <button type="submit" class="login-btn">Sign in</button>
        </form>
        <div class="login-link">
            Don't have an account?
            <a href="{{ route('register') }}">Sign up</a>
        </div>
    </div>
</body>
</html>
