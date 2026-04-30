<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New System User') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-black text-red-800 uppercase tracking-wider">Registration Failed</h3>
                            <ul class="mt-2 text-xs text-red-700 list-disc list-inside font-bold">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-8">
                    @php 
                        if(Auth::user()->role === 'admin') {
                            $storeRoute = route('admin.users.store');
                            $backRoute = route('admin.users');
                        } else {
                            $storeRoute = route('doctor.users.store');
                            $backRoute = route('doctor.users.index'); 
                        }
                    @endphp

                    <form action="{{ $storeRoute }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- First Name --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition">
                            </div>

                            {{-- Last Name --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition">
                            </div>

                            {{-- Date of Birth (Added to resolve validation error) --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Date of Birth</label>
                                <input type="date" name="dob" value="{{ old('dob') }}" required
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition">
                            </div>

                            {{-- Gender (Added to resolve validation error) --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Gender</label>
                                <select name="gender" required
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-black text-gray-700 focus:ring-red-500 focus:border-red-500 transition">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            {{-- National ID --}}
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">National ID (16 Digits)</label>
                                <input type="text" name="national_id" value="{{ old('national_id') }}" required maxlength="16"
                                    placeholder="1 19XX X XXXXXXX X XX"
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition font-mono">
                            </div>

                            {{-- Email --}}
                            <div class="space-y-1 md:col-span-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition">
                            </div>

                            {{-- Phone --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required maxlength="10"
                                    placeholder="078..."
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition">
                            </div>

                            {{-- Account Role --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Account Role</label>
                                <input type="text" value="Donor" class="w-full border-gray-100 bg-gray-200 rounded-xl text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                                <input type="hidden" name="role" value="donor">
                            </div>

                            {{-- District --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">District</label>
                                <input type="text" name="district" value="{{ old('district') }}" required
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-bold focus:ring-red-500 focus:border-red-500 transition">
                            </div>

                            {{-- Blood Type --}}
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Blood Type</label>
                                <select name="blood_type"
                                    class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm font-black text-gray-700 focus:ring-red-500 focus:border-red-500 transition">
                                    <option value="">Unknown</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                        <option value="{{ $type }}" {{ old('blood_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-10 flex items-center justify-between gap-4">
                            <a href="{{ $backRoute }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition">
                                Cancel & Return
                            </a>
                            <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-10 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition shadow-xl shadow-red-100 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                Create User Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>