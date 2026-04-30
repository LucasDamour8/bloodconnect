<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Reschedule Appointment</h2>
                    <p class="text-gray-500">Update your location or date for tracking ID: <span class="font-bold text-red-600">{{ $appointment->tracking_id }}</span></p>
                </div>

                <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        {{-- Location Selection --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Select Donation Center</label>
                            <select name="location_id" class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ $appointment->location_id == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }} ({{ $loc->city }})
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Date Selection --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">New Appointment Date</label>
                            <input type="date" name="appointment_date" 
                                   value="{{ $appointment->appointment_date }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500">
                            @error('appointment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-100">
                                Update Appointment
                            </button>
                            <a href="{{ route('dashboard') }}" class="flex-1 bg-gray-100 text-gray-600 text-center py-3 rounded-xl font-bold hover:bg-gray-200 transition">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>