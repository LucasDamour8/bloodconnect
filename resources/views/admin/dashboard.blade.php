<x-app-layout>
    <div class="space-y-8">
        {{-- 1. Optimized Header --}}
        <div class="mb-8 flex flex-wrap justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">System Overview</h1>
                <p class="text-gray-500 mt-1">Real-time metrics for donors, doctors, and donation logistics.</p>
            </div>
            
            {{-- SEARCH COMPONENT: Added for Admin/Doctor --}}
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'doctor')
            <div class="w-full md:w-96">
                <form action="{{ route('admin.users') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Quick search donor name, ID, or phone..." 
                           class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-100 shadow-sm focus:ring-green-500 focus:border-green-500 text-sm">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </form>
            </div>
            @endif

            <div class="text-xs text-gray-400 font-bold uppercase tracking-widest bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                <i class="fa-solid fa-clock-rotate-left mr-1"></i>
                Last updated: {{ now()->format('M d, H:i') }}
            </div>
        </div>

        {{-- 2. Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach([
                ['Total Donors',      $stats['total_donors'] ?? 0,          'text-blue-600'],
                ['Total Donations',   $stats['total_donations'] ?? 0,       'text-red-600'],
                ['Donation Centers',  $stats['total_locations'] ?? 0,       'text-green-600'],
                ['Pending Appts',     $stats['pending_appts'] ?? 0,         'text-yellow-600'],
                ['Monthly Donations', $stats['completed_this_month'] ?? 0,  'text-purple-600'],
            ] as [$label, $value, $tc])
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all text-center md:text-left">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $label }}</p>
                <p class="text-3xl font-black {{ $tc }} mt-2">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        {{-- 3. Role-Based Action Links --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(Auth::user()->role === 'donor')
                <a href="{{ route('appointments.create') }}" class="flex items-center gap-4 bg-green-50 p-4 rounded-2xl border border-green-100 hover:bg-green-100 transition">
                    <div class="bg-white p-3 rounded-xl shadow-sm"><i class="fa-solid fa-calendar-plus text-green-600"></i></div>
                    <div><p class="text-sm font-bold text-green-900">Book Appointment</p><p class="text-xs text-green-600">Schedule your next donation</p></div>
                </a>
                <a href="{{ route('locations.index') }}" class="flex items-center gap-4 bg-red-50 p-4 rounded-2xl border border-red-100 hover:bg-red-100 transition">
                    <div class="bg-white p-3 rounded-xl shadow-sm"><i class="fa-solid fa-map-location-dot text-red-600"></i></div>
                    <div><p class="text-sm font-bold text-red-900">See Locations</p><p class="text-xs text-red-600">Find centers in Kigali or Nyamagabe</p></div>
                </a>
                <a href="#my-appointments" class="flex items-center gap-4 bg-blue-50 p-4 rounded-2xl border border-blue-100 hover:bg-blue-100 transition">
                    <div class="bg-white p-3 rounded-xl shadow-sm"><i class="fa-solid fa-calendar-check text-blue-600"></i></div>
                    <div><p class="text-sm font-bold text-blue-900">My Schedule</p><p class="text-xs text-blue-600">Scroll to manage bookings</p></div>
                </a>
            @endif

            @if(Auth::user()->role === 'doctor' || Auth::user()->role === 'admin')
                <a href="{{ route('admin.appointments.index') }}" class="flex items-center gap-4 bg-purple-50 p-4 rounded-2xl border border-purple-100 hover:bg-purple-100 transition">
                    <div class="bg-white p-3 rounded-xl shadow-sm"><i class="fa-solid fa-clipboard-list text-purple-600"></i></div>
                    <div><p class="text-sm font-bold text-purple-900">Manage All Appointments</p><p class="text-xs text-purple-600">Review pending donor requests</p></div>
                </a>
                {{-- Fixed: Pointed to existing admin.users route instead of missing doctor route --}}
                <a href="{{ route('admin.users') }}" class="flex items-center gap-4 bg-green-50 p-4 rounded-2xl border border-green-100 hover:bg-green-100 transition">
                    <div class="bg-white p-3 rounded-xl shadow-sm"><i class="fa-solid fa-users text-green-600"></i></div>
                    <div><p class="text-sm font-bold text-green-900">Donor Directory</p><p class="text-xs text-green-600">Manage and view all registered donors</p></div>
                </a>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-4 bg-blue-50 p-4 rounded-2xl border border-blue-100 hover:bg-blue-100 transition">
                    <div class="bg-white p-3 rounded-xl shadow-sm"><i class="fa-solid fa-file-invoice text-blue-600"></i></div>
                    <div><p class="text-sm font-bold text-blue-900">Overall Reports</p><p class="text-xs text-blue-600">View daily or monthly statistics</p></div>
                </a>
            @endif
        </div>

        {{-- 4. DONOR ONLY: Appointment Management Section --}}
        @if(Auth::user()->role === 'donor')
        <div id="my-appointments" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">My Upcoming Appointments</h2>
                <span class="text-[10px] font-black bg-blue-50 text-blue-600 px-3 py-1 rounded-full uppercase">{{ $upcomingAppointments->count() }} Active</span>
            </div>
            <div class="p-6">
                @forelse($upcomingAppointments as $appointment)
                    <div class="flex flex-wrap md:flex-nowrap items-center justify-between p-4 mb-4 last:mb-0 border border-gray-50 rounded-2xl hover:border-red-100 transition">
                        <div class="flex items-center gap-5">
                            <div class="text-center bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                <span class="block text-[10px] font-black text-gray-400 uppercase">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M') }}</span>
                                <span class="block text-xl font-black text-gray-800">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d') }}</span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-mono font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded">#{{ $appointment->tracking_id }}</span>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400">{{ $appointment->donation_type }}</span>
                                </div>
                                <h4 class="font-bold text-gray-900 leading-tight">{{ $appointment->location->name }}</h4>
                                <p class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-4 md:mt-0">
                            <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $appointment->status == 'scheduled' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                                {{ $appointment->status }}
                            </span>
                            
                            <div class="flex gap-2 ml-4">
                                <a href="{{ route('appointments.reschedule', $appointment->id) }}" class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-600 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                
                                <form action="{{ route('appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('Do you want to cancel this appointment?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-600 rounded-xl hover:bg-red-50 hover:text-red-600 transition">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar-xmark text-gray-300 text-xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm italic">No upcoming appointments scheduled.</p>
                        <a href="{{ route('appointments.create') }}" class="text-xs font-bold text-red-500 mt-2 inline-block hover:underline">BOOK NOW</a>
                    </div>
                @endforelse
            </div>
        </div>
        @endif

        {{-- 5. Bottom Data Sections --}}
        <div class="grid lg:grid-cols-3 gap-6 mt-10">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                        <h2 class="font-bold text-gray-800">New Registered Donors</h2>
                        <a href="{{ route('admin.users') }}" class="text-[10px] font-black text-red-600 hover:text-red-700 uppercase tracking-tighter">View All Users</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @forelse($recentUsers ?? [] as $u)
                        <div class="flex items-center justify-between p-5 hover:bg-gray-50/50 transition">
                            <div class="flex items-center gap-4">
                                <div class="relative">
                                    <img src="{{ $u->profile_photo_path ? asset('storage/'.$u->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                         class="w-16 h-16 rounded-full border-2 border-white shadow-md object-cover">
                                    <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 leading-none">{{ $u->first_name }} {{ $u->last_name }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $u->email }}</p>
                                    <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest mt-1">Role: {{ $u->role }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block text-[10px] font-black text-red-600 bg-red-50 px-2 py-1 rounded mb-1">
                                    {{ $u->blood_type ?? '??' }}
                                </span>
                                <p class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $u->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="p-10 text-center text-gray-400 text-sm italic">No donors registered yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Recent Activity Log --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden h-full flex flex-col">
                    <div class="p-6 border-b border-gray-50 bg-white">
                        <h2 class="font-bold text-gray-800">Recent Activity</h2>
                    </div>
                    <div class="p-6 flex-1">
                        <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-100 before:to-transparent">
                            @forelse($recentDonations ?? [] as $d)
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="absolute left-0 w-10 h-10 flex items-center justify-center bg-white border border-gray-100 rounded-full shadow-sm">
                                        <i class="fa-solid fa-droplet text-red-500 text-xs"></i>
                                    </div>
                                    <div class="ml-14">
                                        <p class="text-sm font-bold text-gray-900 leading-tight">{{ $d->user->first_name }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase tracking-widest">{{ $d->location->name ?? 'Unknown Center' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-black {{ $d->status == 'completed' ? 'text-green-500' : 'text-orange-400' }}">
                                    {{ strtoupper($d->status) }}
                                </span>
                            </div>
                            @empty
                            <div class="text-center text-gray-400 text-sm italic py-10">No recent activity.</div>
                            @endforelse
                        </div>
                        <div class="mt-10">
                            <a href="{{ route('admin.donations') }}" class="block text-center py-3 bg-gray-50 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">
                                VIEW ALL LOGS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>