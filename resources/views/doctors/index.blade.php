<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Admin: Manage Doctors') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50" x-data="{ editModal: false, activeDoctor: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header Section - IMPROVED FOR RESPONSIVENESS --}}
            <div class="mb-8 flex flex-col lg:flex-row lg:justify-between lg:items-end gap-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight uppercase">Medical Staff</h1>
                    <p class="text-sm text-gray-500">Manage hospital partners and specialized medical personnel.</p>
                </div>

                {{-- Grouping Search and Buttons to stack on mobile --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    {{-- Search Form - Width becomes full on mobile --}}
                    <form action="{{ route('admin.doctors.index') }}" method="GET" class="relative w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search doctor..." 
                               class="w-full sm:w-64 pl-10 pr-4 py-2.5 rounded-xl border-gray-100 shadow-sm focus:ring-green-500 focus:border-green-500 text-xs font-bold">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <i class="fa-solid fa-user-doctor text-xs"></i>
                        </div>
                    </form>

                    {{-- Add New Doctor Button - Full width on mobile --}}
                    <a href="{{ route('admin.users.create') }}" 
                       class="w-full sm:w-auto flex justify-center items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition shadow-lg shadow-red-100">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Add New Doctor
                    </a>

                    <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 flex items-center shrink-0">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total:</span>
                        <span class="ml-2 font-black text-red-600">{{ $doctors->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Success Notification --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-xl shadow-sm flex items-center justify-between" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
                </div>
            @endif

            {{-- Table Container - ADDED OVERFLOW-X-AUTO FOR MOBILE FIT --}}
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto"> {{-- <-- This is the key fix for the table --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Doctor Info</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">District & Phone</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($doctors as $doctor)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-11 w-11 rounded-full border-2 border-white shadow-sm overflow-hidden bg-red-50 flex-shrink-0">
                                            @if($doctor->profile_photo_path)
                                                <img src="{{ asset('storage/' . $doctor->profile_photo_path) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-red-600 font-bold text-sm">
                                                    {{ substr($doctor->first_name, 0, 1) }}{{ substr($doctor->last_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $doctor->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-semibold">{{ $doctor->district ?? 'Not Assigned' }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $doctor->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($doctor->is_active)
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full bg-green-100 text-green-700 uppercase tracking-tighter">
                                            Verified Active
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full bg-yellow-100 text-yellow-700 uppercase tracking-tighter">
                                            Pending Approval
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end items-center gap-3">
                                        <button @click="activeDoctor = {{ json_encode($doctor) }}; editModal = true" 
                                                class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-widest">
                                            Edit
                                        </button>

                                        <form action="{{ route('admin.doctors.toggle', $doctor->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-tighter text-white transition {{ $doctor->is_active ? 'bg-orange-500 hover:bg-orange-600' : 'bg-green-600 hover:bg-green-700' }}">
                                                {{ $doctor->is_active ? 'Disable' : 'Approve' }}
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm('Delete this account?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold">No doctors matching your search.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if(method_exists($doctors, 'links'))
                <div class="mt-6">
                    {{ $doctors->links() }}
                </div>
            @endif
        </div>

        {{-- Edit Modal --}}
        <div x-show="editModal" 
             x-transition:opacity
             class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 py-6">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="editModal = false"></div>
                
                {{-- Modal Content - Changed max-width and added better padding for mobile --}}
                <div class="bg-white rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full z-50 p-6 md:p-8 border border-gray-100 relative">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-black text-gray-900">Edit Profile</h3>
                        <button @click="editModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>
                    
                    <form :action="'/admin/doctors/' + activeDoctor.id" method="POST" enctype="multipart/form-data">
                        @csrf 
                        @method('PATCH')
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Names --}}
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">First Name</label>
                                <input type="text" name="first_name" x-model="activeDoctor.first_name" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Last Name</label>
                                <input type="text" name="last_name" x-model="activeDoctor.last_name" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                            </div>

                            {{-- Email --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Email</label>
                                <input type="email" name="email" x-model="activeDoctor.email" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                            </div>

                            {{-- Sex & Phone --}}
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Sex</label>
                                <select name="sex" x-model="activeDoctor.sex" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Phone</label>
                                <input type="text" name="phone" x-model="activeDoctor.phone" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                            </div>

                            {{-- National ID & District --}}
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">National ID</label>
                                <input type="text" name="national_id" x-model="activeDoctor.national_id" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">District</label>
                                <input type="text" name="district" x-model="activeDoctor.district" class="w-full border-gray-100 bg-gray-50 rounded-xl text-sm focus:ring-green-500">
                            </div>

                            {{-- Photo --}}
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-1">Update Photo</label>
                                <input name="photo" type="file" class="text-xs block w-full text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row gap-3">
                            <button type="button" @click="editModal = false" class="order-2 sm:order-1 flex-1 px-4 py-3 text-sm font-bold bg-gray-100 rounded-xl">Cancel</button>
                            <button type="submit" class="order-1 sm:order-2 flex-1 px-4 py-3 text-sm font-bold text-white bg-red-600 rounded-xl shadow-lg shadow-red-200">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>