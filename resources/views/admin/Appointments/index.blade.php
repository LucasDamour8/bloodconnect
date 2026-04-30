<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Admin: Appointments Registry') }}
        </h2>
    </x-slot>

    {{-- Added 'donation' to the load() method in @click to ensure name data is available to AlpineJS --}}
    <div class="py-6 md:py-12 bg-gray-50 min-h-screen" x-data="{ asideOpen: false, activeAppt: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header & Stats Section --}}
            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tight">Appointments Registry</h1>
                    <p class="text-sm text-gray-500 font-medium">Review donor records and staff assignments.</p>
                </div>
                
                <div class="flex flex-wrap gap-2 md:gap-3 w-full md:w-auto">
                    <div class="flex-1 md:flex-none bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                        <span class="text-[10px] font-black text-yellow-500 uppercase tracking-widest">Scheduled:</span>
                        <span class="ml-2 font-black text-gray-900">{{ $appointments->where('status', 'scheduled')->count() }}</span>
                    </div>
                    <div class="flex-1 md:flex-none bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                        <span class="text-[10px] font-black text-green-500 uppercase tracking-widest">Approved:</span>
                        <span class="ml-2 font-black text-gray-900">{{ $appointments->where('status', 'approved')->count() }}</span>
                    </div>
                    <div class="flex-1 md:flex-none bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                        <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Done:</span>
                        <span class="ml-2 font-black text-gray-900">{{ $appointments->where('status', 'completed')->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Search & Filter Bar --}}
            <div class="mb-6 flex flex-col xl:flex-row gap-4">
                <form method="GET" action="{{ route('admin.appointments.index') }}" class="flex flex-row gap-2 w-full xl:flex-1">
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search donor..." 
                               class="w-full pl-10 pr-4 py-3 rounded-2xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-red-500 transition-all text-sm">
                        <div class="absolute left-3 top-3.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-gray-900 text-white px-4 md:px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition">
                        Search
                    </button>
                </form>

                <div class="flex flex-wrap gap-2 items-center bg-white p-2 rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
                    <a href="{{ route('admin.appointments.index', array_merge(request()->query(), ['status' => ''])) }}" 
                       class="whitespace-nowrap px-3 md:px-4 py-2 text-[10px] font-black rounded-lg transition-all uppercase tracking-widest {{ !request('status') ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-50' }}">
                        All
                    </a>
                    @foreach(['scheduled', 'approved', 'completed', 'cancelled'] as $status)
                        <a href="{{ route('admin.appointments.index', array_merge(request()->query(), ['status' => $status])) }}" 
                           class="whitespace-nowrap px-3 md:px-4 py-2 text-[10px] font-black rounded-lg transition-all uppercase tracking-widest {{ request('status') == $status ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-50' }}">
                            {{ $status }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Table Container --}}
                <div :class="asideOpen ? 'lg:w-2/3 w-full' : 'w-full'" class="transition-all duration-300">
                    <div class="bg-white overflow-x-auto shadow-sm rounded-2xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Donor</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Date/Time</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest hidden md:table-cell">Handled By</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                @forelse($appointments as $appt)
                                <tr class="hover:bg-red-50/30 transition duration-150 cursor-pointer" 
                                    @click="activeAppt = {{ json_encode($appt->load(['user', 'location', 'doctor', 'donation'])) }}; asideOpen = true">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-black text-gray-500 uppercase flex-shrink-0">
                                                {{ substr($appt->user->first_name, 0, 1) }}{{ substr($appt->user->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $appt->user->first_name }} {{ $appt->user->last_name }}</div>
                                                <div class="text-[10px] font-medium text-gray-400">{{ $appt->user->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-bold">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M') }}</div>
                                        <div class="text-[10px] text-gray-400 font-black uppercase">{{ $appt->appointment_time }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        @if($appt->donation && $appt->donation->approved_by_firstname)
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                <span class="text-xs font-black text-gray-700 uppercase">Dr. {{ $appt->donation->approved_by_firstname }}</span>
                                            </div>
                                        @elseif($appt->doctor)
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                                <span class="text-xs font-black text-gray-700 uppercase">Dr. {{ $appt->doctor->first_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-gray-300 font-black uppercase tracking-tighter">Waiting</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = [
                                                'scheduled' => 'bg-yellow-100 text-yellow-700',
                                                'approved' => 'bg-green-100 text-green-700',
                                                'completed' => 'bg-blue-100 text-blue-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                            ][$appt->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-[9px] leading-5 font-black rounded-lg uppercase tracking-widest {{ $statusClasses }}">
                                            {{ $appt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-red-600 hover:text-red-800 font-black text-[10px] uppercase tracking-widest">
                                            Info &rarr;
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-bold uppercase text-xs tracking-widest">No matching records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Aside Details Panel --}}
                <aside x-show="asideOpen" 
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="opacity-0 translate-y-8 lg:translate-y-0 lg:translate-x-8"
                       x-transition:enter-end="opacity-100 translate-y-0 lg:translate-x-0"
                       class="lg:w-1/3 w-full" x-cloak>
                    
                    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 p-6 md:p-8 sticky top-6">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Registry Details</h3>
                            <button @click="asideOpen = false" class="w-8 h-8 flex items-center justify-center bg-gray-50 text-gray-400 hover:text-red-600 rounded-full transition">&times;</button>
                        </div>

                        <div class="space-y-6">
                            {{-- Donor Profile --}}
                            <div class="flex items-center gap-4 p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="h-12 w-12 md:h-14 md:w-14 rounded-2xl bg-red-600 flex items-center justify-center text-white text-lg md:text-xl font-black shadow-lg shadow-red-100">
                                    <template x-if="activeAppt.user">
                                        <span x-text="activeAppt.user.first_name[0] + activeAppt.user.last_name[0]"></span>
                                    </template>
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="font-black text-gray-900 text-sm md:text-base truncate" x-text="activeAppt.user ? activeAppt.user.first_name + ' ' + activeAppt.user.last_name : ''"></h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate" x-text="activeAppt.user ? activeAppt.user.email : ''"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-white border border-gray-100 rounded-2xl">
                                    <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Center</span>
                                    <span class="text-xs md:text-sm font-black text-gray-800" x-text="activeAppt.location ? activeAppt.location.name : 'N/A'"></span>
                                </div>
                                <div class="p-4 bg-white border border-gray-100 rounded-2xl">
                                    <span class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Blood Type</span>
                                    <span class="text-xs md:text-sm font-black text-red-600 bg-red-50 px-2 py-0.5 rounded" x-text="activeAppt.user ? activeAppt.user.blood_type : '?'"></span>
                                </div>
                            </div>

                            {{-- Handled By --}}
                            <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl">
                                <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Handled By:</label>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                                    <div class="text-xs md:text-sm font-black text-blue-900 uppercase" 
                                         x-text="activeAppt.donation && activeAppt.donation.approved_by_firstname 
                                            ? 'Dr. ' + activeAppt.donation.approved_by_firstname + ' ' + activeAppt.donation.approved_by_lastname 
                                            : (activeAppt.doctor ? 'Dr. ' + activeAppt.doctor.first_name + ' ' + activeAppt.doctor.last_name : 'Staff not yet assigned')">
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="pt-4 space-y-3">
                                <template x-if="activeAppt.status === 'completed' || activeAppt.status === 'approved'">
                                    <div class="space-y-3">
                                        <a :href="'/admin/appointments/' + activeAppt.id + '/view'" 
                                           class="flex items-center justify-center gap-2 w-full bg-gray-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-gray-200 hover:bg-black transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Report
                                        </a>

                                        <a :href="'/admin/appointments/' + activeAppt.id + '/download'" 
                                           class="flex items-center justify-center gap-2 w-full bg-white text-gray-600 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest border border-gray-200 hover:bg-gray-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Download PDF
                                        </a>
                                    </div>
                                </template>

                                <template x-if="activeAppt.status !== 'completed' && activeAppt.status !== 'approved'">
                                    <div class="p-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200 text-center">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Appointment Status</span>
                                        <p class="text-[9px] text-gray-400 mt-1 font-bold uppercase" x-text="activeAppt.status"></p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>