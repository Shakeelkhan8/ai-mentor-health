@php
    $countries = ['Australia', 'Bangladesh', 'Belarus', 'Brazil', 'Canada', 'China', 'France', 'Germany', 'India',
        'Indonesia', 'Israel', 'Italy', 'Japan', 'Korea', 'Mexico', 'Pakistan', 'Philippines', 'Russia', 'Saudi Arabia',
        'South Africa', 'Thailand', 'Turkey', 'Ukraine', 'United Arab Emirates', 'United Kingdom', 'United States'];
    $fieldClass = 'mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Account settings</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <h3 class="text-lg font-medium text-gray-900">Profile details</h3>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf

                    <div class="flex items-center gap-4">
                        @if ($user->img)
                            <img src="{{ asset('assets/users/'.$user->img) }}" alt="Your profile photo" class="h-20 w-20 rounded-full object-cover">
                        @else
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-100 text-2xl font-semibold text-brand-700" aria-hidden="true">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <x-input-label for="img" value="Profile photo" />
                            <input id="img" name="img" type="file" accept="image/png, image/jpeg, image/gif, image/webp"
                                class="mt-1 block text-sm text-gray-700 file:me-3 file:rounded-md file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-white">
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF or WebP, up to 2 MB.</p>
                            <x-input-error :messages="$errors->get('img')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="name" value="Name" />
                            <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="organization" value="Organization" />
                            <x-text-input id="organization" name="organization" class="mt-1 block w-full" :value="old('organization', $user->organization)" />
                        </div>
                        <div>
                            <x-input-label for="phone_no" value="Phone number" />
                            <x-text-input id="phone_no" name="phone_no" type="tel" class="mt-1 block w-full" :value="old('phone_no', $user->phone_no)" autocomplete="tel" />
                        </div>
                        <div>
                            <x-input-label for="address" value="Address" />
                            <x-text-input id="address" name="address" class="mt-1 block w-full" :value="old('address', $user->address)" autocomplete="street-address" />
                        </div>
                        <div>
                            <x-input-label for="country" value="Country" />
                            <select id="country" name="country" class="{{ $fieldClass }}">
                                <option value="">Select</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country }}" @selected(old('country', $user->country) === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-primary-button>Save changes</x-primary-button>
                </form>
            </x-card>

            <x-card>
                <h3 class="text-lg font-medium text-gray-900">Change password</h3>

                <form action="{{ route('update-password') }}" method="POST" class="mt-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="currentPassword" value="Current password" />
                        <x-text-input id="currentPassword" name="currentPassword" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('currentPassword')" class="mt-1" />
                    </div>
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="newPassword" value="New password" />
                            <x-text-input id="newPassword" name="newPassword" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('newPassword')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="newPassword_confirmation" value="Confirm new password" />
                            <x-text-input id="newPassword_confirmation" name="newPassword_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">At least 8 characters.</p>

                    <x-primary-button>Update password</x-primary-button>
                </form>
            </x-card>

            <x-card>
                @include('profile.partials.delete-user-form')
            </x-card>
        </div>
    </div>
</x-app-layout>
