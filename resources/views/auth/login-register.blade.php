<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="icon" href="{{ asset('images/taskly_logo.svg') }}" type="image/svg+xml" />
    {{-- @vite(['resources/css/authPage.css']) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Join Us</title>

    <style>
        .auth-form {
            width: 50%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            padding: 2rem;
            transition: all 0.5s ease;

        }

        #message {
            position: absolute;
            width: 50%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem;
            z-index: 10;
            transition: all 0.7s ease;
        }



        /* Hide until JS positions it correctly */
        body:not(.ready) #message {
            visibility: hidden;
        }

        @media (max-width: 768px) {
            #container {
                flex-direction: column;
                height: 600px;
                position: relative;


            }

            .auth-form,
            #message {
                width: 100%;
                height: 100%;

                position: absolute;
                top: 0;
                left: 0;
                margin: 0;
                border-radius: 1.5rem;
                transition: all 0.7s ease;

            }

            #login-form,
            #register-form {
                z-index: 20;
            }

            #message {
                z-index: 10;
            }


        }
    </style>


</head>


<body class="flex justify-center items-center  min-h-[100vh] bg-[#e5e7eb] p-[1rem] m-0 overflow-x-hidden  ">
    <x-loader></x-loader>


    <section id="container" data-default-form="{{ request()->routeIs('register') ? 'register' : 'login' }}"
        class="flex w-[980px] max-w-[80rem] h-[600px] bg-[#f3f4f6] rounded-3xl overflow-hidden relative">
        <!-- Login Form -->
        <form id="login-form" method="POST" action="{{ route('login') }}" class="auth-form form-hidden">
            {{-- style="opacity: 1; pointer-events: auto;" --}}

            {{-- @if ($errors->any())
                <div class="text-red-500 text-sm mb-3">
                    {{ $errors->first() }}
                </div>@endif --}}

            @csrf
            <h1 class="font-bold text-3xl select-none">Sign In</h1>
            {{-- social sign in icons  --}}
            <div class="flex flex-col gap-2 justify-center items-center">
                <div class="signInIcons flex gap-2">
                    <a href="{{ route('social.redirect', 'google') }}">
                        <x-svg.google size="35"
                            class="group transition-all duration-200 p-1 rounded-full shadow-md hover:shadow-lg hover:bg-gray-200">
                        </x-svg.google>
                    </a>
                    <a href="{{ route('social.redirect', 'github') }}">
                        <x-svg.github size="35"
                            class="shadow-md p-1 rounded-full hover:shadow-lg hover:bg-gray-200 transition-all duration-200 text-blue-600 hover:text-blue-400">
                        </x-svg.github>
                    </a>
                </div>
                <hr class="h-px bg-gray-300 w-full mt-2" />
                <p>Or sign in with your email</p>
            </div>
            {{-- Email Feild  --}}
            <div class="w-full max-w-sm">
                <x-input-modern id="email-login" name="email" label="Email"
                    placeholder="Enter your email..."></x-input-modern>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>
            {{-- Password Feild  --}}
            <div class="w-full max-w-sm">
                <x-input-modern type="password" id="password-login" name="password" label="Password"
                    placeholder="Enter your password..."></x-input-modern>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
            {{-- Remember Me --}}

            <label for="remember_me" class="inline-flex items-center self-start ml-10">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-0 focus:ring-transparent "
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 select-none">{{ __('Remember me') }}</span>
            </label>


            <div class="flex flex-col gap-2 w-full max-w-sm items-center">
                <x-button class="w-[50%] text-nowrap" type="submit" text="Sign In"></x-button>
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
           <div class="mt-4 md:hidden">
    <button type="button"
        class="switch-mobile group flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-500 transition-colors duration-200 cursor-pointer">

        <span class="relative after:absolute after:left-0 after:bottom-0 after:h-px after:w-0 after:bg-blue-500 after:transition-all after:duration-300 group-hover:after:w-full">
            Switch to Register
        </span>

        <span class="transition-transform duration-300 group-hover:translate-y-0.5">
            <x-svg.arrow direction="down" />
        </span>

    </button>
</div>

        </form>

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}" id="register-form" class="auth-form form-visible">
            {{-- style="opacity: 0; pointer-events: none;" --}}
            @csrf
            <h1 class="font-bold text-3xl select-none">Sign Up</h1>
            {{-- Username Feild  --}}
            <div class="w-full max-w-sm">
                <x-input-modern id="name-register" name="name" label="Username"
                    placeholder="Enter your username..."></x-input-modern>
                <x-input-error :messages="$errors->register->get('name')" class="mt-1" />

            </div>
            {{-- Email Feild  --}}
            <div class="w-full max-w-sm">
                <x-input-modern id="email-register" name="email" label="Email"
                    placeholder="Enter your email..."></x-input-modern>
                <x-input-error :messages="$errors->register->get('email')" class="mt-1" />
            </div>
            {{-- Password Feild  --}}
            <div class="w-full max-w-sm">
                <x-input-modern type="password" id="password-register" name="password" label="Password"
                    placeholder="Enter your password..."></x-input-modern>
                <x-input-error :messages="$errors->register->get('password')" class="mt-1" />

            </div>
            {{-- Confirm Password Feild  --}}
            <div class="w-full max-w-sm">
                <x-input-modern type="password" id="confirm-password-register" name="password_confirmation"
                    label="Confirm password" placeholder="Confirm your password...">
                </x-input-modern>
                <x-input-error :messages="$errors->register->get('password_confirmation')" class="mt-1" />

            </div>
            <x-button class="w-[50%]" type="submit" text="Sign Up" bgColor="bg-[#10b981]"
                hoverColor="hover:bg-[#059669]" activeColor="active:bg-[#047857]"></x-button>
          <div class="mt-4 md:hidden">
    <button type="button"
        class="switch-mobile group flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-500 transition-colors duration-200 cursor-pointer">

        <span class="relative after:absolute after:left-0 after:bottom-0 after:h-px after:w-0 after:bg-blue-500 after:transition-all after:duration-300 group-hover:after:w-full group-active:">
            Switch to Login
        </span>

        <span class="transition-transform duration-300 group-hover:-translate-y-0.5">
            <x-svg.arrow direction="up" />
        </span>

    </button>
</div>

        </form>

        <!-- Message Panel -->
        <div id="message" aria-live="polite"
            class="relative bg-gradient-to-l from-indigo-600 to-blue-500 text-white  ">
            {{-- <x-application-logo class="w-[200px] h-[80px] mb-6"></x-application-logo> --}}
            <img src="images/tasklyw.svg" class="w-[200px] h-[80px] mb-6" alt="Logo" />

            <div class=" absolute bg-white/10 rounded-full w-[120px] h-[120px] top-[-40px] left-[-40px] "></div>
            <div class="absolute bg-white/10 rounded-full w-[150px] h-[150px] bottom-[-60px] right-[-60px]"></div>
            <h2 class="text-3xl font-semibold mb-4">Welcome Back!</h2>
            <p class="mb-6 mx-10 font-light">Don't have an account yet?</p>
            <button id="switchBtn"
                class="bg-white/10 px-3 py-[0.35rem] rounded-lg hover:bg-white/25 transition-all duration-150">←
                Sign
                Up</button>
        </div>
    </section>
    @vite('resources/js/auth/authPage.js')


</body>

</html>
