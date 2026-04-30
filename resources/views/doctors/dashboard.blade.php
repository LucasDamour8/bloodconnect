<x-app-layout>
    <x-slot name="header">
        {{-- Fixed Header: Stacks on mobile, side-by-side on desktop --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Doctor Dashboard') }}
            </h2>
            {{-- Quick View Button: Full width on mobile --}}
            <button @click="$dispatch('open-modal', 'location-modal')" class="w-full sm:w-auto bg-gray-900 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-600 transition">
                My assigned locations ({{ count($assignedLocations) }})
            </button>
        </div>
    </x-slot>

    <div class="py-6 md:py-12 bg-gray-50 min-h-screen" x-data="{ asideOpen: false, activeAppt: {} }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Validation / Success Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="mb-8">
                <h1 class="text-2xl md:text-3xl font-black text-gray-900">Doctor Control Center</h1>
                <p class="text-gray-500 text-sm mt-1 font-medium">Showing appointments for your assigned zones.</p>
            </div>

            {{-- Stats Grid: Adjusted column count and text size for mobile --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                <div class="bg-white rounded-2xl border border-gray-100 p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending</p>
                    <p class="text-3xl md:text-4xl font-black text-yellow-500 mt-2">{{ $stats['pending'] }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Approved Today</p>
                    <p class="text-3xl md:text-4xl font-black text-green-500 mt-2">{{ $stats['approved_today'] }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Deferred Today</p>
                    <p class="text-3xl md:text-4xl font-black text-red-500 mt-2">{{ $stats['deferred_today'] }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Monthly Total</p>
                    <p class="text-3xl md:text-4xl font-black text-blue-500 mt-2">{{ $stats['monthly_total'] }}</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Main Content Section --}}
                <div :class="asideOpen ? 'lg:w-2/3' : 'w-full'" class="transition-all duration-300 w-full">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                            <h2 class="font-black text-gray-900 uppercase tracking-tight text-sm md:text-base">Active Appointments</h2>
                        </div>

                        <div class="divide-y divide-gray-50 overflow-x-auto">
                            @forelse($pendingAppointments as $appt)
                            <div class="flex items-center justify-between p-4 md:p-6 hover:bg-gray-50 transition cursor-pointer min-w-[400px] sm:min-w-0"
                                 @click="activeAppt = {{ json_encode($appt->load(['user', 'location'])) }}; asideOpen = true">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gray-900 flex items-center justify-center text-white font-black text-xs shrink-0">
                                        {{ substr($appt->user->first_name, 0, 1) }}{{ substr($appt->user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-900 text-sm md:text-base">{{ $appt->user->first_name }} {{ $appt->user->last_name }}</p>
                                        <p class="text-[9px] md:text-[10px] text-red-600 font-black uppercase tracking-tighter">
                                            {{ $appt->location->name }} • {{ \Carbon\Carbon::parse($appt->appointment_date)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="{{ $appt->status === 'scheduled' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }} px-3 py-1 rounded-lg text-[9px] md:text-[10px] font-black uppercase shrink-0">
                                    {{ $appt->status === 'scheduled' ? 'Needs Exam' : 'Approved' }}
                                </span>
                            </div>
                            @empty
                            <div class="text-center py-12">
                                <p class="text-gray-400 text-sm italic">No appointments in your assigned locations.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Action Panel: Now fits better on mobile screens when active --}}
                <aside x-show="asideOpen" x-transition x-cloak class="w-full lg:w-1/3">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-6 md:p-8 sticky top-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-black text-gray-900 uppercase tracking-widest text-xs">Donor Details</h3>
                            <button @click="asideOpen = false" class="text-gray-400 hover:text-red-600 font-black text-2xl">&times;</button>
                        </div>

                        <div class="space-y-6">
                            <div class="p-4 bg-gray-50 rounded-2xl">
                                <p class="text-[10px] font-black text-gray-400 uppercase">Patient</p>
                                <p class="font-black text-gray-900" x-text="activeAppt.user ? activeAppt.user.first_name + ' ' + activeAppt.user.last_name : ''"></p>
                                <p class="text-[10px] font-bold text-red-600" x-text="activeAppt.location ? activeAppt.location.name : ''"></p>
                            </div>

                            <template x-if="activeAppt.status === 'scheduled'">
                                <a :href="'/doctor/appointments/' + activeAppt.id + '/examine'" class="block w-full text-center bg-red-600 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] md:text-xs shadow-lg shadow-red-100 transition">
                                    Begin Physical Exam
                                </a>
                            </template>
                            
                            <template x-if="activeAppt.status === 'approved'">
                                <form :action="'/doctor/appointments/' + activeAppt.id + '/status'" method="POST">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black uppercase text-[10px] md:text-xs tracking-widest shadow-lg shadow-blue-100">
                                        Confirm Blood Collection
                                    </button>
                                </form>
                            </template>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Notifications & Feedback Sections --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">Internal Announcements</h3>
                    <div class="space-y-4">
                        @foreach($notifications as $announcement)
                            <div class="border-l-4 border-red-500 pl-4 py-1">
                                <p class="text-sm font-bold text-gray-900">{{ $announcement->title }}</p>
                                <p class="text-xs text-gray-500">{{ $announcement->content }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">User Feedback</h3>
                    <div class="space-y-4">
                        @foreach($feedbacks as $fb)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs font-black text-gray-900">{{ $fb->name }}</p>
                                <p class="text-xs text-gray-500 italic">"{{ $fb->message }}"</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assigned Locations Modal: Improved for small phone screens --}}
    <x-modal name="location-modal" focusable>
        <div class="p-4 md:p-6">
            <h2 class="text-lg font-black text-gray-900 uppercase tracking-tight mb-4">My Assigned Locations</h2>
            <div class="grid gap-3 md:gap-4">
                @foreach($assignedLocations as $loc)
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-gray-50 rounded-2xl border border-gray-100 gap-2">
                        <div>
                            <p class="font-black text-gray-900">{{ $loc->name }}</p>
                            <p class="text-xs text-gray-500">{{ $loc->district }}, {{ $loc->sector }}</p>
                        </div>
                        <span class="bg-green-100 text-green-700 text-[10px] font-black px-2 py-1 rounded-lg uppercase">Active</span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-modal>
</x-app-layout>