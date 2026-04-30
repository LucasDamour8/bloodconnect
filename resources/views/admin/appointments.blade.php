<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Admin: Manage Appointments') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50" x-data="{ asideOpen: false, activeAppt: {} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Appointments</h1>
                    <p class="text-sm text-gray-500">Track donation requests, status, and assignments.</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending:</span>
                    <span class="ml-2 font-black text-red-600">{{ $appointments->where('status', 'pending')->count() }}</span>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Table Container --}}
                <div :class="asideOpen ? 'lg:w-2/3' : 'w-full'" class="transition-all duration-300">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Donor</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date/Time</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Details</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($appointments as $appt)
                                <tr class="hover:bg-gray-50/50 transition duration-150 cursor-pointer" 
                                    @click="activeAppt = {{ json_encode($appt->load(['user', 'location', 'doctor'])) }}; asideOpen = true">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $appt->user->first_name }} {{ $appt->user->last_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $appt->user->phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $appt->timeslot }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black rounded-full uppercase tracking-tighter
                                            {{ $appt->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $appt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-widest">
                                            View &rarr;
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">No appointments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>

                {{-- Aside Details Panel --}}
                <aside x-show="asideOpen" 
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="opacity-0 translate-x-8"
                       x-transition:enter-end="opacity-100 translate-x-0"
                       class="lg:w-1/3 w-full" x-cloak>
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 sticky top-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-black text-gray-900">Details</h3>
                            <button @click="asideOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl">
                                <div class="h-12 w-12 rounded-full bg-red-600 flex items-center justify-center text-white font-bold">
                                    <template x-if="activeAppt.user">
                                        <span x-text="activeAppt.user.first_name[0] + activeAppt.user.last_name[0]"></span>
                                    </template>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900" x-text="activeAppt.user ? activeAppt.user.first_name + ' ' + activeAppt.user.last_name : ''"></h4>
                                    <p class="text-xs text-gray-500" x-text="activeAppt.user ? activeAppt.user.email : ''"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-3 bg-white border border-gray-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-gray-400 uppercase">Location</span>
                                    <span class="text-sm font-bold text-gray-800" x-text="activeAppt.location ? activeAppt.location.name : 'N/A'"></span>
                                </div>
                                <div class="p-3 bg-white border border-gray-100 rounded-xl">
                                    <span class="block text-[10px] font-black text-gray-400 uppercase">Blood Type</span>
                                    <span class="text-sm font-bold text-red-600" x-text="activeAppt.user ? activeAppt.user.blood_type : '?'"></span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase">Assigned Doctor</label>
                                <div class="text-sm font-medium text-gray-700" x-text="activeAppt.doctor ? 'Dr. ' + activeAppt.doctor.last_name : 'No doctor assigned yet'"></div>
                            </div>

                            <div class="pt-4 flex gap-2">
                                <button class="flex-1 bg-red-600 text-white py-3 rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-red-100">Approve</button>
                                <button class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-xl text-xs font-bold uppercase tracking-widest">Defer</button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>