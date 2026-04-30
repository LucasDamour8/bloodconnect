<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Medical Queue: Manage Appointments') }}
        </h2>
    </x-slot>

    <div class="py-6 md:py-12 bg-gray-50 min-h-screen" x-data="{ asideOpen: false, activeAppt: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header & Stats Section --}}
            <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tight">Patient Queue</h1>
                    <p class="text-xs md:text-sm text-slate-500 font-medium">Review donor eligibility and finalize blood collection.</p>
                </div>
                
                <div class="flex flex-wrap gap-2 md:gap-3 w-full md:w-auto">
                    <div class="flex-1 md:flex-none bg-white px-3 md:px-4 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="text-[9px] md:text-[10px] font-black text-amber-500 uppercase tracking-widest">Waiting:</span>
                        <span class="ml-1 md:ml-2 font-black text-slate-900">{{ $appointments->where('status', 'scheduled')->count() }}</span>
                    </div>
                    <div class="flex-1 md:flex-none bg-white px-3 md:px-4 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="text-[9px] md:text-[10px] font-black text-emerald-500 uppercase tracking-widest">Approved:</span>
                        <span class="ml-1 md:ml-2 font-black text-slate-900">{{ $appointments->where('status', 'approved')->count() }}</span>
                    </div>
                    <div class="flex-1 md:flex-none bg-white px-3 md:px-4 py-2 rounded-xl shadow-sm border border-slate-100">
                        <span class="text-[9px] md:text-[10px] font-black text-indigo-500 uppercase tracking-widest">Done:</span>
                        <span class="ml-1 md:ml-2 font-black text-slate-900">{{ $appointments->where('status', 'completed')->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Search, Filter & Center Selector Bar --}}
            <div class="mb-6 space-y-4">
                <form id="filterForm" method="GET" action="{{ route('doctor.appointments') }}" class="flex flex-col gap-4">
                    {{-- Search Row --}}
                    <div class="flex flex-col xl:flex-row gap-4">
                        <div class="w-full flex gap-2">
                            <div class="relative flex-1">
                                <input type="text" name="search" value="{{ request('search') }}" 
                                       placeholder="Search donor name..." 
                                       class="w-full pl-10 pr-4 py-3 rounded-2xl border-none ring-1 ring-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                                <div class="absolute left-3 top-3.5 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                            <button type="submit" class="bg-slate-900 text-white px-4 md:px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition">
                                Find
                            </button>
                        </div>

                        {{-- Status Filters --}}
                        <div class="flex flex-wrap gap-1 items-center bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100 overflow-x-auto">
                            <input type="hidden" name="status" value="{{ request('status', 'scheduled') }}">
                            @foreach(['scheduled' => 'To Examine', 'approved' => 'Ready to Draw', 'completed' => 'Finished'] as $status => $label)
                                <a href="{{ route('doctor.appointments', array_merge(request()->query(), ['status' => $status])) }}" 
                                   class="whitespace-nowrap px-3 md:px-4 py-2 text-[9px] md:text-[10px] font-black rounded-lg transition-all uppercase tracking-widest {{ request('status', 'scheduled') == $status ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-400 hover:bg-slate-50' }}">
                                     {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Center Multi-Selector (The Separator) --}}
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-3 px-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Filter by Your Assigned Centers:</span>
                            @if(request('centers'))
                                <a href="{{ route('doctor.appointments', request()->except('centers')) }}" class="text-[9px] font-black text-red-500 uppercase hover:underline">Clear Filter</a>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-3">
                            {{-- We assume $assignedCenters is passed from the Controller --}}
                            @foreach($assignedLocations as $center)
                                <label class="flex items-center gap-3 px-4 py-2.5 rounded-xl border border-slate-50 cursor-pointer hover:bg-slate-50 transition-all {{ is_array(request('centers')) && in_array($center->id, request('centers')) ? 'bg-indigo-50/50 border-indigo-200 ring-1 ring-indigo-200' : '' }}">
                                    <input type="checkbox" name="centers[]" value="{{ $center->id }}" 
                                           onchange="document.getElementById('filterForm').submit()"
                                           {{ is_array(request('centers')) && in_array($center->id, request('centers')) ? 'checked' : '' }}
                                           class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                    <span class="text-xs font-bold text-slate-700">{{ $center->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 relative">
                {{-- Table Container --}}
                <div :class="asideOpen ? 'lg:w-2/3 w-full' : 'w-full'" class="transition-all duration-300">
                    <div class="bg-white overflow-x-auto shadow-sm sm:rounded-2xl border border-slate-100">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Donor Detail</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Appointment</th>
                                    <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Medical Status</th>
                                    <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-50">
                                @forelse($appointments as $appt)
                                <tr class="hover:bg-indigo-50/30 transition duration-150 cursor-pointer" 
                                    @click="activeAppt = {{ json_encode($appt->load(['user', 'location'])) }}; asideOpen = true">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-xs font-black text-white uppercase shadow-md shrink-0">
                                                {{ substr($appt->user->first_name, 0, 1) }}{{ substr($appt->user->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-900">{{ $appt->user->first_name }} {{ $appt->user->last_name }}</div>
                                                <div class="text-[10px] font-black text-red-600 uppercase">{{ $appt->user->blood_type ?? 'Unknown' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900 font-bold">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</div>
                                        <div class="text-[10px] text-slate-400 font-black uppercase italic">{{ $appt->location->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-[9px] font-black rounded-lg uppercase tracking-widest 
                                            {{ $appt->status === 'scheduled' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $appt->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $appt->status === 'completed' ? 'bg-indigo-100 text-indigo-700' : '' }}">
                                            {{ $appt->status === 'scheduled' ? 'Awaiting Exam' : $appt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if($appt->status === 'scheduled')
                                            <a href="{{ route('doctor.appointments.examine', $appt->id) }}" class="inline-block bg-slate-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition">
                                                Examine
                                            </a>
                                        @elseif($appt->status === 'approved')
                                            <form action="{{ route('doctor.appointments.status', $appt->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:bg-emerald-700 transition flex items-center gap-1 ml-auto">
                                                    Mark Completed <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        @elseif($appt->status === 'completed')
                                            <span class="text-indigo-600 font-black text-[10px] uppercase tracking-widest">
                                                Finalized <i class="fa-solid fa-check-circle ml-1"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-bold uppercase text-xs tracking-widest">No patients in the queue for the selected centers.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Side Panel (Stays the same as your code) --}}
                <div x-show="asideOpen" @click="asideOpen = false" class="lg:hidden fixed inset-0 bg-black/50 z-40"></div>

                <aside x-show="asideOpen" 
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="opacity-0 translate-x-8 md:translate-x-full"
                       x-transition:enter-end="opacity-100 translate-x-0"
                       :class="asideOpen ? 'fixed lg:sticky inset-y-0 right-0 z-50 lg:z-auto w-[85%] md:w-1/2 lg:w-1/3' : 'hidden'"
                       class="bg-white lg:bg-transparent overflow-y-auto lg:overflow-visible" x-cloak>
                    
                    <div class="bg-white rounded-l-3xl lg:rounded-3xl shadow-2xl border border-slate-100 p-6 md:p-8 min-h-full lg:min-h-0 lg:sticky lg:top-6">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Patient Context</h3>
                            <button @click="asideOpen = false" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-red-600 rounded-full transition shadow-sm">&times;</button>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                                <div class="h-14 w-14 rounded-2xl bg-slate-900 flex items-center justify-center text-white text-xl font-black shadow-lg shadow-slate-200 shrink-0">
                                    <template x-if="activeAppt.user">
                                        <span x-text="activeAppt.user.first_name[0] + activeAppt.user.last_name[0]"></span>
                                    </template>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-black text-slate-900 text-base truncate" x-text="activeAppt.user ? activeAppt.user.first_name + ' ' + activeAppt.user.last_name : ''"></h4>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest truncate" x-text="activeAppt.user ? 'Donor ID: #00' + activeAppt.user.id : ''"></p>
                                </div>
                            </div>

                            <div class="p-4 bg-white border border-slate-100 rounded-2xl">
                                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Assigned Center</span>
                                <span class="text-sm font-black text-slate-800" x-text="activeAppt.location ? activeAppt.location.name : 'N/A'"></span>
                            </div>

                            <div class="pt-6 border-t border-slate-50">
                                <template x-if="activeAppt.status === 'scheduled'">
                                    <div class="space-y-3">
                                        <p class="text-[10px] font-black text-slate-400 uppercase text-center mb-4">Awaiting Medical Examination</p>
                                        <a :href="'/doctor/appointments/' + activeAppt.id + '/examine'" class="block w-full text-center bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-slate-100 hover:bg-black transition">
                                            Start Physical Exam
                                        </a>
                                    </div>
                                </template>

                                <template x-if="activeAppt.status === 'approved'">
                                    <form :action="'/doctor/appointments/' + activeAppt.id + '/status'" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <p class="text-[10px] font-black text-emerald-500 uppercase text-center mb-4">Examination Passed - Ready for Collection</p>
                                        <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition">
                                            Confirm Blood Draw
                                        </button>
                                    </form>
                                </template>

                                <template x-if="activeAppt.status === 'completed'">
                                    <div class="space-y-4">
                                        <div class="p-4 bg-indigo-50 rounded-2xl text-center border border-indigo-100 mb-4">
                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Donation Finalized</span>
                                            <p class="text-xs text-indigo-400 mt-1 font-bold">Record archived successfully.</p>
                                        </div>
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