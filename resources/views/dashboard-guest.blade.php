@extends('layout')
@section('page-name', 'Dashboard')

@section('main')

    <style>
        .hero-pattern {
            background-image:
                radial-gradient(circle at 20% 50%, rgba(123, 77, 211, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(139, 92, 246, 0.05) 0%, transparent 50%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #7B4DD3 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    {{-- ── HERO ── --}}
    <section class="hero-pattern flex flex-col-reverse lg:flex-row items-center justify-center gap-3 lg:gap-3 min-h-[50vh] py-6 bg-gray-100 dark:bg-gray-800 lg:px-[6rem]">
        <div class="absolute top-20 left-10 w-32 h-32 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-40 h-40 bg-emerald-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse" style="animation-delay: 2s;"></div>

        <div class="w-full lg:w-1/2 flex flex-col justify-center gap-8 text-center lg:text-left">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight dark:text-white">
                Get Your Life, <br>
                <span class="gradient-text text-4xl sm:text-5xl lg:text-7xl">Simplified</span>
            </h1>
            <p class="text-gray-600 dark:text-gray-300 text-lg lg:text-xl max-w-md mx-auto lg:mx-0">
                The all-in-one workspace where teams plan, track, and deliver projects with clarity and confidence.
            </p>
            <div class="flex flex-col items-center lg:items-start">
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3 text-nowrap">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-14 py-3 text-lg bg-[#7433ed] font-semibold hover:bg-[#7B4DD3] hover:scale-[1.02] transition-all duration-200 text-white rounded-xl shadow-lg">
                        Continue where you left
                    </a>
                    <a href="#See_How_It_Works" class="inline-flex items-center justify-center gap-2 px-4 py-3 text-lg text-emerald-600 dark:text-emerald-400 font-semibold border-2 border-emerald-600 dark:border-emerald-500 rounded-xl hover:bg-emerald-600 dark:hover:bg-emerald-500 hover:text-white dark:hover:text-black transition-all duration-300">
                        See How It Works
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
                <div class="flex flex-row gap-3 text-nowrap">
                    <div class="flex flex-row items-center">
                        <x-svg.check-mark-circle></x-svg.check-mark-circle>
                        <p class="px-2 py-2 text-gray-700 dark:text-gray-500 font-semibold">No credit card required</p>
                    </div>
                    <div class="flex flex-row items-center">
                        <x-svg.check-mark-circle></x-svg.check-mark-circle>
                        <p class="px-2 py-2 text-gray-700 dark:text-gray-500 font-semibold">Free forever plan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center">
            <img src="{{ asset('images/Work anniversary-bro.png') }}" alt="Hero Image" class="w-full max-w-xl lg:max-w-2xl object-contain drop-shadow-md" />
        </div>
    </section>



    @endsection

