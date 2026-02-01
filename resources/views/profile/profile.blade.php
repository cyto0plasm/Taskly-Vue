@extends('layout')
@section('page-name', 'Profile')

@section('main')

    <section class="max-w-4xl mx-auto mt-10 space-y-10">

        {{-- PROFILE OVERVIEW --}}
        <div class="p-6 rounded-xl shadow-md  light:border bg-white dark:bg-[#232422] dark:text-white">
            <div class="flex flex-col md:flex-row gap-10">

                {{-- AVATAR + ACTIONS --}}
                <div class="flex flex-col items-center gap-4">

                    {{-- Avatar --}}
                    <div id="avatarWrapper"
                        class="w-32 h-32 rounded-full overflow-hidden cursor-pointer shadow hover:shadow-md transition">

                        @if (auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/profile_photos/' . auth()->user()->profile_photo_path) }}"
                                class="object-cover w-full h-full">
                        @else
                            <img id="photoPreview" src="{{ asset('images/user-black.png') }}"
                                class="object-cover w-full h-full dark:hidden">

                            <img id="photoPreviewDark" src="{{ asset('images/user-blue.png') }}"
                                class="hidden dark:block object-cover w-full h-full">
                        @endif
                    </div>

                    {{-- Upload --}}
                    <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data"
                        class="flex flex-col items-center gap-2">
                        @csrf
                        <input id="photoInput" type="file" name="profile_photo" accept="image/*" class="hidden">

                        <button id="savePhotoBtn" disabled
                            class="px-4 py-1 bg-blue-600 text-white rounded-md text-sm disabled:opacity-40 hover:bg-blue-700 transition">
                            Save Photo
                        </button>

                        @error('profile_photo')
                            <p class="text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </form>

                    {{-- Delete --}}
                    <form action="{{ route('profile.photo.delete') }}" method="POST">
                        @csrf
                        <button class="text-red-500 hover:text-red-600 text-sm">Remove Photo</button>
                    </form>


                </div>

                {{-- PROFILE DETAILS --}}
                <div class="flex-1">
                    <h1 class="text-2xl font-bold mb-1">Your Profile</h1>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        Manage your personal details.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-10">

                        <div>
                            <p class="text-xs uppercase text-gray-500">Name</p>
                            <p class="mt-1 text-lg font-medium">{{ auth()->user()->name }}</p>
                        </div>

                        <div>
                            <p class="text-xs uppercase text-gray-500">Email</p>
                            <p class="mt-1 text-lg">{{ auth()->user()->email }}</p>

                        </div>

                        <div class="sm:col-span-2">
                            <p class="text-xs uppercase text-gray-500">Bio</p>
                            <p class="mt-1 text-gray-700 dark:text-gray-200 whitespace-pre-line">
                                {{ auth()->user()->bio ?: 'No bio yet.' }}
                            </p>

                        </div>



                    </div>

                    <div class="mt-auto flex justify-end">
                        <form action="{{ route('verification.notice') }}" method="GET">

                            <x-button class="ml-4" type="submit" text="Reset Password" bgColor="bg-[#eab308]"
                                hoverColor="bg-[#f5cb4c]" activeColor="bg-[#e5ac00]"></x-button>

                            {{-- 'text' => 'Button',
                            'type' => 'button',
                            'bgColor' => 'bg-[#6b3eea]',
                            'hoverColor' => 'hover:bg-[#6935f7]',
                            'activeColor' => 'active:bg-[#6335e0]',
                            'textColor' => 'text-white',
                            'hoverText' => 'hover:text-gray-800',
                            'activeText' => 'active:text-gray-800', --}}
                        </form>
                    </div>

                </div>

            </div>
        </div>

        {{-- UPDATE PERSONAL INFO --}}
        <div class="p-6 rounded-xl shadow-md  light:border bg-white dark:bg-[#232422] dark:text-white">
            <h2 class="text-xl font-semibold mb-4">Update Personal Information</h2>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <x-input-modern id="name" name="name" label="Name" :value="auth()->user()->name" />
                <x-input-error :messages="$errors->get('name')" />

                <x-input-modern id="email" name="email" label="Email" :value="auth()->user()->email" />
                <x-input-error :messages="$errors->get('email')" />

                <x-input-modern id="bio" name="bio" label="Bio" field="textarea" :value="auth()->user()->bio" />
                <x-input-error :messages="$errors->get('bio')" />

                <x-button class="w-40" text="Save Changes" type="submit"></x-button>
            </form>
        </div>

        {{-- CHANGE PASSWORD --}}

        {{-- <div class="p-6 rounded-xl shadow-sm light:border bg-white dark:bg-[#232422] dark:text-white">
            <h2 class="text-xl font-semibold mb-4">Change Password</h2>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email (prefilled and readonly to match token) -->
                <x-input-modern type="email" id="email" name="email" label="Email"
                    value="{{ request()->email ?? '' }}" readonly />
                <x-input-error :messages="$errors->get('email')" />

                <!-- New Password -->
                <x-input-modern type="password" id="password" name="password" label="New Password"
                    placeholder="Enter Your New Password..." autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" />

                <!-- Confirm Password -->
                <x-input-modern type="password" id="password_confirmation" name="password_confirmation"
                    label="Confirm New Password" placeholder="Confirm Your New Password..." autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" />

                <!-- Submit Button -->
                <x-button class="w-48" text="Update Password" type="submit"></x-button>
            </form>

        </div> --}}

    </section>
    <script>
        const input = document.getElementById("photoInput");
        const wrapper = document.getElementById("avatarWrapper");
        const saveBtn = document.getElementById("savePhotoBtn");
        const imgPreview = document.getElementById("photoPreview");
        const imgPreviewDark = document.getElementById("photoPreviewDark");
        const photoPreviewLightNav = document.getElementById("photoPreviewLightNav");
        const photoPreviewDarkNav = document.getElementById("photoPreviewDarkNav");

        wrapper.onclick = () => input.click();

        input.onchange = (e) => {
            const file = e.target.files[0];

            if (!file) {
                saveBtn.disabled = true;
                return;
            }
            const url = URL.createObjectURL(file);
            imgPreview.src = url;
            imgPreviewDark.src = url;
            photoPreviewLightNav.src = url;
            photoPreviewDarkNav.src = url;

            saveBtn.disabled = false;
        };
    </script>

@endsection
