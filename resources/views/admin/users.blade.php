<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Donors') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50" x-data="{ editModal: false, activeUser: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-8 flex flex-col md:flex-row justify-between items-center md:items-end gap-4">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight text-center md:text-left">Donor Directory</h1>
                    <p class="text-sm text-gray-500 text-center md:text-left">Manage donor profiles, blood types, and account status.</p>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                    {{-- Search Form --}}
                    <form action="{{ route('admin.users') }}" method="GET" class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search donor or blood type..." 
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-100 shadow-sm focus:ring-green-500 focus:border-green-500 text-xs font-bold">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </div>
                    </form>

                    {{-- Add New User Button --}}
                    <a href="{{ route('admin.users.create') }}" 
                       class="w-full sm:w-auto flex justify-center items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition shadow-lg shadow-red-100">
                        <i class="fa-solid fa-plus"></i>
                        Add New User
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl shadow-sm font-bold">
                    <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Table Container with Responsive Scroll --}}
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-2xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Donor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Blood Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Location & Phone</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full border border-gray-200 overflow-hidden bg-gray-100 flex-shrink-0">
                                        @if($user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-red-600 font-bold text-xs bg-red-50">
                                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-md text-sm font-bold bg-red-600 text-white">
                                    {{ $user->blood_type ?? '??' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $user->district ?? 'Unknown' }}</div>
                                <div class="text-xs">{{ $user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Active' : 'Suspended' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    {{-- Edit --}}
                                    <button @click="activeUser = {{ $user }}; editModal = true" class="p-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition" title="Edit Profile">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    {{-- Reset Password --}}
                                    <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" onsubmit="return confirm('Reset password to password123?')">
                                        @csrf
                                        <button type="submit" class="p-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition" title="Reset Password">
                                            <i class="fa-solid fa-key"></i>
                                        </button>
                                    </form>
                                    
                                    {{-- Block/Unblock --}}
                                    <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-white font-bold transition {{ $user->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }}">
                                            {{ $user->is_active ? 'Block' : 'Unblock' }}
                                        </button>
                                    </form>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this donor? This action cannot be undone.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Delete Donor">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No donors found matching your search.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>

        {{-- Donor Edit Modal --}}
        <div x-show="editModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-black opacity-40" @click="editModal = false"></div>
                <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full z-50 p-6 md:p-8 overflow-hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Update Donor Profile</h3>
                        <button @click="editModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    
                    <form :action="( '{{ Auth::user()->role }}' === 'admin' ? '/admin/users/' : '/doctor/users/' ) + activeUser.id" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Identity Section --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">First Name</label>
                                    <input type="text" name="first_name" x-model="activeUser.first_name" required class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Last Name</label>
                                    <input type="text" name="last_name" x-model="activeUser.last_name" required class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email Address</label>
                                    <input type="email" name="email" x-model="activeUser.email" required class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                </div>
                            </div>

                            {{-- Medical & Contact Section --}}
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">National ID</label>
                                    <input type="text" name="national_id" x-model="activeUser.national_id" class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Blood Type</label>
                                        <select name="blood_type" x-model="activeUser.blood_type" class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                            <option value="">Unknown</option>
                                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">District</label>
                                        <input type="text" name="district" x-model="activeUser.district" class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone Number</label>
                                    <input type="text" name="phone" x-model="activeUser.phone" maxlength="10" class="mt-1 w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-red-500 focus:border-red-500 font-bold">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 p-4 bg-red-50 rounded-2xl border border-red-100">
                            <label class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-2 block">Update Profile Photo</label>
                            <input type="file" name="photo" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-red-600 file:text-white hover:file:bg-red-700 cursor-pointer">
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-3">
                            <button type="button" @click="editModal = false" class="w-full sm:w-auto px-6 py-3 text-[10px] text-gray-500 bg-gray-100 rounded-xl font-black uppercase tracking-widest hover:bg-gray-200 transition">Discard</button>
                            <button type="submit" class="w-full sm:w-auto px-6 py-3 text-[10px] text-white bg-red-600 rounded-xl shadow-lg shadow-red-100 font-black uppercase tracking-widest hover:bg-red-700 transition">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>