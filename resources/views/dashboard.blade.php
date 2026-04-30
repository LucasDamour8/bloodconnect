<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10 px-4" x-data="{ showIneligibleModal: false }">
        <div class="max-w-7xl mx-auto">
            
            {{-- ACCOUNT SUSPENSION CHECK --}}
            @if(Auth::user()->status === 'inactive' || Auth::user()->is_active == false)
                <div class="bg-white rounded-3xl border border-red-100 shadow-2xl p-10 text-center max-w-2xl mx-auto my-20">
                    <div class="w-24 h-24 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-user-slash text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 mb-4 uppercase tracking-tight">Account Suspended</h1>
                    <p class="text-lg text-gray-600 leading-relaxed mb-8">
                        Your account is currently inactive or suspended. Please contact the system administrator to unlock your account and resume your lifesaving journey.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('contact.index') }}" class="bg-red-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-700 transition">
                            Contact Support
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full bg-gray-100 text-gray-600 px-8 py-3 rounded-xl font-bold hover:bg-gray-200 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- START OF NORMAL DASHBOARD --}}

                {{-- Flash Success/Error Messages --}}
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded-r-xl shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded-r-xl shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Header --}}
                <div class="mb-10">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('dashboard.welcome', ['name' => Auth::user()->first_name]) }}</h1>
                    <p class="text-gray-500 mt-1">{{ __('dashboard.hero_thanks') }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    
                    {{-- LEFT COLUMN: Stats & Lists --}}
                    <div class="lg:col-span-3 space-y-6">
                        
                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $statItems = [
                                    [__('dashboard.stats.total_donations'), $stats['total'], __('dashboard.stats.total_desc'), 'text-red-600', '❤️'],
                                    [__('dashboard.stats.blood_type'), Auth::user()->blood_type ?? 'N/A', __('dashboard.stats.blood_desc'), 'text-blue-600', '👥'],
                                    [__('dashboard.stats.lives_saved'), $stats['total'] * 3, __('dashboard.stats.lives_desc'), 'text-green-600', '📈'],
                                    [__('dashboard.stats.next_eligible'), $stats['next_eligible'] ?? __('dashboard.stats.now'), __('dashboard.stats.availability'), 'text-orange-600', '🕒']
                                ];
                            @endphp
                            @foreach($statItems as $item)
                            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm relative">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ $item[0] }}</p>
                                        <p class="text-2xl font-black mt-1 uppercase text-gray-900">{{ $item[1] }}</p>
                                    </div>
                                    <span class="text-lg opacity-40">{{ $item[4] }}</span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">{{ $item[2] }}</p>
                            </div>
                            @endforeach
                        </div>

                        {{-- Upcoming Appointments --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4">
                                <span class="text-red-500">📅</span> {{ __('dashboard.upcoming') }}
                            </h2>
                            <p class="text-xs text-gray-400 -mt-3 mb-4">{{ __('dashboard.upcoming_subtitle') }}</p>
                            
                            @forelse($upcomingAppointments as $appt)
                            <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition mb-3">
                                <div class="flex-1">
                                    <p class="font-bold text-gray-800">{{ $appt->location->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $appt->location->address }} · {{ $appt->location->city }}</p>
                                    <p class="text-xs font-medium text-red-600 mt-1">
                                        {{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }} · {{ __('dashboard.table.tracking_id') }}: <span class="font-bold">{{ $appt->tracking_id }}</span>
                                    </p>
                                    <span class="mt-2 inline-block px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $appt->status == 'scheduled' ? 'bg-orange-50 text-orange-600' : 'bg-blue-50 text-blue-600' }}">
                                        {{ __('dashboard.table.status') }}: {{ $appt->status }}
                                    </span>
                                </div>

                                <div class="flex gap-2">
                                    @if($appt->status === 'scheduled')
                                        <a href="{{ route('appointments.edit', $appt->id) }}" class="px-4 py-1.5 text-xs font-semibold bg-white border border-gray-200 rounded-lg hover:text-blue-600 hover:border-blue-200 transition">
                                            {{ __('dashboard.table.edit') }}
                                        </a>

                                        <form action="{{ route('appointments.destroy', $appt->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-1.5 text-xs font-semibold border border-red-100 text-red-500 rounded-lg hover:bg-red-50 transition">
                                                {{ __('dashboard.table.delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-gray-400 italic bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">{{ __('dashboard.table.locked') }}</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-6 border-2 border-dashed border-gray-100 rounded-xl">
                                <p class="text-gray-400 text-sm">{{ __('dashboard.table.no_appointments') }}</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Recent Donations --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2 mb-4">
                                <span class="text-red-500">❤️</span> {{ __('dashboard.recent') }}
                            </h2>
                            <p class="text-xs text-gray-400 -mt-3 mb-4">{{ __('dashboard.recent_subtitle') }}</p>
                            
                            <div class="space-y-3">
                                @forelse($recentDonations as $dn)
                                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-xl">
                                    <div>
                                        <p class="font-bold text-gray-800 capitalize">{{ str_replace('_', ' ', $dn->donation_type ?? 'Blood Donation') }}</p>
                                        <p class="text-xs text-gray-400">{{ $dn->location->name ?? 'Mobile Unit' }} · {{ \Carbon\Carbon::parse($dn->created_at)->format('M d, Y') }}</p>
                                    </div>
                                    <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Completed</span>
                                </div>
                                @empty
                                <p class="text-gray-400 text-sm italic">{{ __('dashboard.table.no_donations') }}</p>
                                @endforelse
                            </div>
                            <a href="{{ route('donations.index') }}" class="block text-center w-full mt-6 py-2 text-xs font-bold text-gray-500 border border-gray-100 rounded-lg hover:bg-gray-50 transition">{{ __('dashboard.table.view_all') }}</a>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="space-y-6">
                        {{-- Quick Actions --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h3 class="font-bold text-gray-900 mb-4">{{ __('dashboard.quick_actions') }}</h3>
                            <div class="space-y-3">
                                @if($stats['can_donate'])
                                    <a href="{{ route('donor.locations') }}" class="flex items-center justify-center gap-2 w-full bg-red-600 text-white py-3 rounded-xl text-sm font-bold hover:bg-red-700 transition shadow-lg shadow-red-100">
                                        <span>📅</span> {{ __('dashboard.book_appt') }}
                                    </a>
                                @else
                                    <button type="button" @click="showIneligibleModal = true" class="flex items-center justify-center gap-2 w-full bg-gray-400 text-white py-3 rounded-xl text-sm font-bold cursor-not-allowed shadow-md">
                                        <span>📅</span> {{ __('dashboard.book_appt') }}
                                    </button>
                                @endif
                                <a href="{{ route('donor.locations') }}" class="flex items-center justify-center gap-2 w-full border border-gray-100 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                    <span>📍</span> {{ __('dashboard.find_loc') }}
                                </a>
                                <a href="{{ route('donor.eligibility') }}" class="flex items-center justify-center gap-2 w-full border border-gray-100 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                    <span>📋</span> {{ __('dashboard.check_elig') }}
                                </a>
                                <a href="{{ route('contact.index') }}" class="flex items-center justify-center gap-2 w-full border border-gray-100 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                    <span>📩</span> {{ __('dashboard.contact') }}
                                </a>
                            </div>
                        </div>

                        {{-- Progress --}}
                        @php
                            $currentGoal = 15;
                            $percent = ($stats['total'] / $currentGoal) * 100;
                        @endphp
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h3 class="font-bold text-gray-900">{{ __('dashboard.progress_title') }}</h3>
                            <p class="text-[10px] text-gray-400 mb-4">{{ __('dashboard.milestone', ['count' => $currentGoal]) }}</p>
                            <div class="flex justify-between text-[10px] font-bold mb-1">
                                <span class="text-gray-400">{{ __('dashboard.current') }}: {{ $stats['total'] }}</span>
                                <span class="text-gray-900">{{ __('dashboard.goal') }}: {{ $currentGoal }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full transition-all duration-500" style="width: {{ min($percent, 100) }}%"></div>
                            </div>
                            <p class="text-[9px] text-gray-400 mt-2 italic">
                                {{ __('dashboard.more_to_go', ['count' => max(0, $currentGoal - $stats['total'])]) }}
                            </p>
                        </div>

                        {{-- Achievements --}}
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span class="text-yellow-500">🎗️</span> {{ __('dashboard.achievements') }}
                            </h3>
                            <div class="grid grid-cols-3 gap-2">
                                @php
                                    $badges = [
                                        [__('dashboard.badges.first'), '💖', $stats['total'] >= 1],
                                        [__('dashboard.badges.five'), '⭐', $stats['total'] >= 5],
                                        [__('dashboard.badges.ten'), '🏆', $stats['total'] >= 10],
                                        [__('dashboard.badges.lifesaver'), '💝', $stats['total'] >= 15],
                                        [__('dashboard.badges.hero'), '👤', $stats['total'] >= 25],
                                        [__('dashboard.badges.regular'), '🔥', $stats['total'] >= 2]
                                    ];
                                @endphp
                                @foreach($badges as $badge)
                                <div class="aspect-square {{ $badge[2] ? 'bg-yellow-50 border-yellow-100 text-yellow-700' : 'bg-gray-50 border-gray-50 text-gray-300' }} border rounded-xl flex flex-col items-center justify-center text-[8px] font-bold text-center p-1 transition shadow-sm">
                                    <span class="text-lg {{ $badge[2] ? '' : 'grayscale opacity-30' }}">{{ $badge[1] }}</span>
                                    <span class="mt-1 leading-tight">{{ $badge[0] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            {{-- END OF SUSPENSION CHECK --}}
        </div>

        {{-- INELIGIBLE MODAL --}}
        <div x-show="showIneligibleModal" 
             x-cloak 
             class="fixed inset-0 z-[100] overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="bg-white rounded-3xl max-w-sm w-full p-8 text-center shadow-2xl border border-gray-100" @click.away="showIneligibleModal = false">
                <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-3">Wait a moment!</h2>
                <p class="text-gray-600 leading-relaxed mb-8">
                    You are not allowed to donate yet. You must wait at least 56 days between donations to stay healthy.
                    <br><br>
                    You will be eligible again on: <br>
                    <span class="font-black text-red-600 text-lg">{{ $stats['next_eligible'] }}</span>
                </p>

                <button @click="showIneligibleModal = false" class="w-full bg-gray-900 text-white py-4 rounded-2xl font-bold hover:bg-black transition-all shadow-lg">
                    I Understand
                </button>
            </div>
        </div>
    </div>
</x-app-layout>