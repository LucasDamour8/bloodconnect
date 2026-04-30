<div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
    <div class="bg-[#2A3F54] p-6 text-white flex justify-between items-center">
        <h2 class="text-xl font-bold uppercase tracking-tight">Full Appointment Record</h2>
        <span class="bg-white/10 px-4 py-1 rounded-full text-xs font-bold">TRACKING ID: {{ $appointment->tracking_id }}</span>
    </div>

    <div class="p-8 space-y-8">
        {{-- Section: Donor Profile Data --}}
        <div>
            <h3 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4">Donor Personal Data</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] text-gray-400 uppercase font-bold">Full Name</label>
                    <p class="font-bold text-gray-800">{{ $appointment->user->first_name }} {{ $appointment->user->last_name }}</p>
                </div>
                <div>
                    <label class="block text-[10px] text-gray-400 uppercase font-bold">Blood Type</label>
                    <p class="font-bold text-red-600">{{ $appointment->user->blood_type ?? 'Not Set' }}</p>
                </div>
                <div>
                    <label class="block text-[10px] text-gray-400 uppercase font-bold">Contact</label>
                    <p class="font-medium text-gray-700">{{ $appointment->user->phone ?? 'No Phone' }}</p>
                </div>
            </div>
        </div>

        <hr class="border-gray-50">

        {{-- Section: Donation Context --}}
        <div>
            <h3 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4">Donation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[10px] text-gray-400 uppercase font-bold">Donation Type</label>
                    <p class="font-medium text-gray-700 uppercase">{{ str_replace('_', ' ', $appointment->donation_type) }}</p>
                </div>
                <div>
                    <label class="block text-[10px] text-gray-400 uppercase font-bold">Center Location</label>
                    <p class="font-medium text-gray-700">{{ $appointment->location->name }}</p>
                </div>
                <div>
                    <label class="block text-[10px] text-gray-400 uppercase font-bold">Current Status</label>
                    <span class="text-xs font-black uppercase text-blue-600">{{ $appointment->status }}</span>
                </div>
            </div>
        </div>
    </div>
</div>