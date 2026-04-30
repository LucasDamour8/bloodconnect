<x-app-layout>
    <div class="p-4 md:p-8 bg-gray-50 min-h-screen" x-data="{ 
        showModal: false, 
        selectedLocation: null,
        availableDates: [],
        loading: false,
        async openBooking(location) {
            this.selectedLocation = location;
            this.showModal = true;
            this.loading = true;
            this.availableDates = [];
            try {
                const response = await fetch(`/api/available-dates/${location.id}`);
                this.availableDates = await response.json();
            } catch (e) { console.error('Error'); }
            this.loading = false;
        }
    }">
        <div class="max-w-7xl mx-auto mb-10">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-6 bg-white p-6 rounded-2xl border shadow-sm">
                <div class="text-center lg:text-left">
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ __('appt.title') }}</h1>
                    <p class="text-gray-500 text-sm">{{ __('appt.subtitle') }}</p>
                </div>

                <form action="{{ route('donor.locations') }}" method="GET" class="w-full lg:w-1/2 flex gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="{{ __('appt.search_placeholder') }}" 
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 outline-none">
                    </div>
                    <button type="submit" class="bg-red-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-md">
                        {{ __('appt.search_btn') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($locations as $location)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all flex flex-col overflow-hidden">
                <div class="p-6 flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                            @if($location->type === 'center') <i class="fa-solid fa-hospital text-2xl"></i> @else <i class="fa-solid fa-bus text-2xl"></i> @endif
                        </div>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-lg uppercase">
                            {{ __('appt.active') }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $location->name }}</h3>
                    <p class="text-gray-500 text-xs mt-1 mb-4">{{ $location->address }}, {{ $location->city }}</p>
                    <div class="space-y-3 text-xs text-gray-600 border-t pt-4">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-phone text-red-500"></i>
                            <span class="font-medium">{{ $location->phone ?? __('appt.na') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-clock text-blue-500"></i>
                            <span>{{ __('appt.hours') }}: {{ $location->hours ?? '08:00 - 17:00' }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-5 bg-gray-50 border-t flex flex-col gap-3">
                    <button @click="openBooking({{ json_encode($location) }})" class="w-full bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition text-sm">
                        {{ __('appt.book_btn') }}
                    </button>
                    <a href="tel:{{ $location->phone }}" class="w-full border border-gray-200 bg-white text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-100 transition text-sm text-center flex items-center justify-center gap-2">
                        <i class="fa-solid fa-phone"></i> {{ __('appt.call_btn') }}
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-2xl border">
                <p class="text-gray-400">{{ __('appt.no_results') }}</p>
            </div>
            @endforelse
        </div>

        <div class="max-w-7xl mx-auto mt-20 border-t pt-10 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-2xl border text-center shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('appt.stats_total') }}</p>
                    <p class="text-4xl font-black text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white p-8 rounded-2xl border text-center shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('appt.stats_available') }}</p>
                    <p class="text-4xl font-black text-red-600">{{ $stats['walk_ins'] }}</p>
                </div>
                <div class="bg-white p-8 rounded-2xl border text-center shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">{{ __('appt.stats_today') }}</p>
                    <p class="text-4xl font-black text-gray-900">{{ $stats['open_today'] }}</p>
                </div>
            </div>
        </div>

        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak x-transition>
            <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl" @click.away="showModal = false">
                <div class="p-6 border-b bg-red-600 text-white flex justify-between items-center">
                    <h3 class="text-xl font-bold">{{ __('appt.modal_title') }}</h3>
                    <button @click="showModal = false" class="text-2xl">&times;</button>
                </div>
                
                <form action="{{ route('donor.appointments.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    <input type="hidden" name="location_id" :value="selectedLocation?.id">
                    
                    <p class="text-xl font-extrabold text-gray-800" x-text="selectedLocation?.name"></p>

                    <div x-show="loading" class="py-10 text-center">
                        <i class="fa-solid fa-circle-notch fa-spin text-red-600 text-3xl"></i>
                    </div>

                    <div x-show="!loading && availableDates.length === 0" class="p-6 bg-yellow-50 border border-yellow-100 rounded-2xl text-center">
                        <p class="text-sm text-yellow-800 font-bold">
                            {{ __('appt.no_slots') }}
                        </p>
                    </div>

                    <div x-show="!loading && availableDates.length > 0" class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('appt.select_date') }}</label>
                            <select name="appointment_date" class="w-full border rounded-xl p-3 outline-none" required>
                                <template x-for="date in availableDates">
                                    <option :value="date.value" x-text="date.label"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('appt.select_time') }}</label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'] as $time)
                                <label class="cursor-pointer">
                                    <input type="radio" name="appointment_time" value="{{ $time }}" class="peer hidden" required>
                                    <div class="text-center py-2 border rounded-xl text-sm font-bold peer-checked:bg-red-600 peer-checked:text-white transition">
                                        {{ $time }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl font-black hover:bg-red-700 transition">
                            {{ __('appt.confirm_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>