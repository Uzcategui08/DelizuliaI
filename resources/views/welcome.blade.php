<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/welcome.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <body>

<section class="hero">
    <div class="hero-text">
        <h4>Bienvenido a la </h4>
        <h1>Familia</h1>
        <p>Te invito a iniciar sesión y/o registrarte para comenzar este proceso!</p>
        
        <div class="auth-buttons">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-dashboard">
                        <i class="ri-dashboard-line"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="ri-login-circle-line"></i> Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-register">
                            <i class="ri-user-add-line"></i> Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>
        
    </div>
    
    <div class="hero-img">
        <img src="/images/Autoeyslogo.png" alt="">
    </div>
</section>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
    
</html>

<script src="https://unpkg.com/scrollreveal"></script>
    <script src="js/app.js"></script>