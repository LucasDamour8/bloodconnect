<section>
    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="profile_photo" :value="__('Profile Photo')" />
            <div class="mt-2 mb-4">
                <img src="{{ Auth::user()->profilePhotoUrl() }}" class="h-20 w-20 rounded-full object-cover border-2 border-red-500 shadow-sm">
            </div>
            <input id="profile_photo" name="profile_photo" type="file" class="mt-1 block w-full text-sm text-gray-500" accept="image/*" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required />
            </div>
            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email (Cannot be changed)')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="$user->email" readonly />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="blood_type" :value="__('Blood Type')" />
                <x-text-input id="blood_type" name="blood_type" type="text" class="mt-1 block w-full" :value="old('blood_type', $user->blood_type)" />
            </div>
            
            <div>
                <x-input-label for="phone" :value="__('Phone Number (Locked)')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="$user->phone" readonly />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-red-600 hover:bg-red-700">
                {{ __('SAVE CHANGES') }}
            </x-primary-button>
        </div>
    </form>
</section>