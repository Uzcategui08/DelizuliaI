<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - AutoKeys</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap");
    
    :root {
      --white-color: hsl(0,0%,100%);
      --black-color: hsl(0, 0% , 0%);
      --body-font: "Poppins", sans-serif; 
      --h1-font-size: 1.75rem;
      --normal-font-size: 1rem;
      --small-font-size: .813rem;
      --font-medium: 500; 
      --error-color: hsl(0, 100%, 70%);
      --success-color: hsl(120, 100%, 70%);
      --info-color: hsl(200, 100%, 70%);
    }

    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    body,
    input,
    button {
      font-size: var(--normal-font-size);
      font-family: var(--body-font);
    }

    body {
      color: var(--white-color);
      background-color: #1a1a2e;
    }

    input,
    button {
      border: none;
      outline: none;
    }

    a {
      text-decoration: none;
    }

    img {
      max-width: 100%;
      height: auto;
    }

    .login {
      position: relative;
      height: 100vh;
      display: grid;
      align-items: center;
    }

    .login__img {
      position: absolute;
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      z-index: -1;
    }

    .login__form {
      position: relative;
      background-color: hsla(0, 0%, 10%, 0.1);
      border: 2px solid var(--white-color);
      margin-inline: 1.5rem;
      padding: 2.5rem 1.5rem;
      border-radius: 1rem;
      backdrop-filter: blur(8px);
      max-width: 500px;
      margin: 0 auto;
    }

    .login__title {
      text-align: center;
      font-size: var(--h1-font-size);
      font-weight: var(--font-medium);
      margin-bottom: 1rem;
      color: #fff;
    }

    .login__message {
      text-align: center;
      font-size: var(--small-font-size);
      margin-bottom: 2rem;
      color: var(--white-color);
      opacity: 0.8;
    }

    .login__content, .login__box {
      display: grid;
    }

    .login__content {
      row-gap: 1.75rem;
      margin-bottom: 1.5rem;
    }

    .login__box {
      grid-template-columns: max-content 1fr;
      align-items: center;
      column-gap: 0.75rem;
      border-bottom: 2px solid var(--white-color);
      padding-bottom: 0.5rem;
    }

    .login__icon {
      font-size: 1.25rem;
      color: var(--white-color);
    }

    .login__input {
      width: 100%;
      padding-block: 0.8rem;
      background: none;
      color: var(--white-color);
      position: relative;
      z-index: 1;
    }

    .login__box-input {
      position: relative;
    }

    .login__label {
      position: absolute;
      left: 0;
      top: 13px;
      font-weight: var(--font-medium);
      transition: top 0.3s, font-size 0.3s;
      color: var(--white-color);
    }

    .login__button {
        margin-top:20px;
      width: 100%;
      padding: 1rem;
      border-radius: 0.5rem;
      background-color: var(--white-color);
      font-weight: var(--font-medium);
      cursor: pointer;
      margin-bottom: 1rem;
      transition: all 0.3s ease;
      color: #1a1a2e;
      font-weight: 600;
    }

    .login__button:hover {
      background-color: rgba(255, 255, 255, 0.9);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
    }

    .login__back {
      text-align: center;
      margin-top: 1rem;
    }

    .login__back a {
      color: var(--white-color);
      font-weight: var(--font-medium);
      font-size: var(--small-font-size);
    }

    .login__back a:hover {
      text-decoration: underline;
    }

    .login__input:focus + .login__label,
    .login__input:not(:placeholder-shown) + .login__label {
      top: -12px;
      font-size: var(--small-font-size);
    }

    /* Session Status Styles */
    .auth-session-status {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: 0.5rem;
      text-align: center;
      font-size: var(--small-font-size);
    }

    .auth-session-status.success {
      background-color: rgba(0, 255, 0, 0.1);
      border: 1px solid var(--success-color);
      color: var(--success-color);
    }

    .auth-session-status.error {
      background-color: rgba(255, 0, 0, 0.1);
      border: 1px solid var(--error-color);
      color: var(--error-color);
    }

    .auth-session-status.info {
      background-color: rgba(0, 191, 255, 0.1);
      border: 1px solid var(--info-color);
      color: var(--info-color);
    }

    /* Input Errors */
    .input-error {
      color: var(--error-color);
      font-size: var(--small-font-size);
      margin-top: 0.25rem;
      display: block;
    }

    /* Responsive */
    @media screen and (min-width: 576px) {
      .login {
        justify-content: center;
      }
      
      .login__form {
        width: 432px;
        padding: 4rem 3rem 3.5rem;
        border-radius: 1.5rem;
      }
      
      .login__title {
        font-size: 2rem;
      }
    }
  </style>
    <body style="font-family: 'Poppins', sans-serif; color: var(--white-color); background-color: #1a1a2e;">
    <div class="login">
        <img src="/images/Carro.jpg" alt="login image" class="login__img">

        <form method="POST" action="{{ route('register') }}" class="login__form">
            @csrf

            <h1 class="login__title">Register</h1>

            <!-- Name -->
            <div class="login__box">
                <i class="ri-user-line login__icon"></i>

                <div class="login__box-input">
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="login__input" placeholder="">
                    <label for="name" class="login__label">Name</label>
                </div>
            </div>
            @error('name')
            <span class="input-error">{{ $message }}</span>
            @enderror

            <!-- Email Address -->
            <div class="login__box">
                <i class="ri-mail-line login__icon"></i>

                <div class="login__box-input">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="login__input" placeholder="">
                    <label for="email" class="login__label">Email</label>
                </div>
            </div>
            @error('email')
            <span class="input-error">{{ $message }}</span>
            @enderror

            <!-- Password -->
            <div class="login__box">
                <i class="ri-lock-line login__icon"></i>

                <div class="login__box-input">
                    <input type="password" id="password" name="password" required autocomplete="new-password" class="login__input" placeholder="">
                    <label for="password" class="login__label">Password</label>
                </div>
            </div>
            @error('password')
            <span class="input-error">{{ $message }}</span>
            @enderror

            <!-- Confirm Password -->
            <div class="login__box">
                <i class="ri-lock-line login__icon"></i>

                <div class="login__box-input">
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="login__input" placeholder="">
                    <label for="password_confirmation" class="login__label">Confirm Password</label>
                </div>
            </div>
            @error('password_confirmation')
            <span class="input-error">{{ $message }}</span>
            @enderror

            <button type="submit" class="login__button">
                {{ __('Register') }}
            </button>

            <div class="login__back">
                <a href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        /* Floating labels enhancement */
        document.querySelectorAll('.login__input').forEach(input => {
            // If there's a value already (like from old input), float the label
            if(input.value) {
                input.nextElementSibling.style.top = '-12px'
                input.nextElementSibling.style.fontSize = 'var(--small-font-size)'
            }

            // Handle autofill
            input.addEventListener('animationstart', (e) => {
                if(e.animationName === 'onAutoFillStart') {
                    input.nextElementSibling.style.top = '-12px'
                    input.nextElementSibling.style.fontSize = 'var(--small-font-size)'
                }
            })
        })
    </script>
    </body>

