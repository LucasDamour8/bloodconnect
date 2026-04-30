<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- THE FIX: Check this section and ensure only ONE partial is called --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            
            {{-- THE DUPLICATION: Remove this duplicated div section entirely --}}
            {{-- 
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
            --}}

            {{-- 
               This duplication usually happens when there's an old partial reference 
               still present alongside the new one. Delete the extra div block shown above. 
            --}}

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-2xl border border-gray-100">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>