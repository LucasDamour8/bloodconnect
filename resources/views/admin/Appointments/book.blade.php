<x-app-layout>
    <div class="py-6 md:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="bg-[#2A3F54] px-6 md:px-8 py-5 border-b border-[#3E4F5F]">
                    <h3 class="text-white font-bold text-base md:text-lg flex items-center gap-3">
                        <i class="fa-solid fa-calendar-check text-red-500"></i>
                        Book Your Donation
                    </h3>
                </div>

                <div class="p-6 md:p-8">
                    <form action="{{ route('appointments.store') }}" method="POST" id="bookingForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                            
                            {{-- Center Selection --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Select Center</label>
                                <select name="location_id" id="location_id" class="w-full px-4 py-3 md:py-4 rounded-xl border border-gray-200 outline-none transition text-sm md:text-base" required>
                                    <option value="">-- Choose Center --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Donation Type --}}
                            <div class="col-span-1">
                                <label class="block text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Donation Type</label>
                                <select name="donation_type" class="w-full px-4 py-3 md:py-4 rounded-xl border border-gray-200 outline-none transition text-sm md:text-base" required>
                                    <option value="whole_blood">Whole Blood</option>
                                    <option value="plasma">Plasma</option>
                                    <option value="platelets">Platelets</option>
                                </select>
                            </div>

                            {{-- Available Dates --}}
                            <div class="col-span-1">
                                <label class="block text-[10px] md:text-xs font-black uppercase tracking-widest text-gray-500 mb-2">Available Dates & Slots</label>
                                <div class="relative">
                                    <select name="capacity_id" id="capacity_id" class="w-full px-4 py-3 md:py-4 rounded-xl border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed outline-none transition text-sm md:text-base" required disabled>
                                        <option value="">Select center first...</option>
                                    </select>
                                    <div id="loader" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                                        <i class="fa-solid fa-circle-notch animate-spin text-red-500"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- THE BUTTON WITH LOGIC --}}
                        <button type="submit" 
                            id="confirmBtn"
                            class="mt-8 w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 md:py-5 rounded-xl shadow-lg text-sm md:text-base uppercase tracking-widest transition-transform active:scale-95">
                            CONFIRM BOOKING
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check Eligibility on Submit
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const daysLeft = {{ $daysUntilNextDonation }};
            
            if (daysLeft > 0) {
                e.preventDefault(); // Stop the form from sending
                alert("Ba Uretse gato! Ugomba gutegereza iminsi " + daysLeft + " kugirango wongere gutanga amaraso.");
            }
        });

        // Your existing Dynamic Dropdown Logic
        document.getElementById('location_id').addEventListener('change', function() {
            const locId = this.value;
            const capSelect = document.getElementById('capacity_id');
            const loader = document.getElementById('loader');

            if (!locId) {
                capSelect.disabled = true;
                capSelect.innerHTML = '<option value="">Select center first...</option>';
                return;
            }

            loader.classList.remove('hidden');

            fetch(`/api/available-dates/${locId}`)
                .then(res => res.json())
                .then(data => {
                    capSelect.innerHTML = '<option value="">-- Select Date (Spots Left) --</option>';
                    if (data.length > 0) {
                        data.forEach(item => {
                            capSelect.innerHTML += `<option value="${item.id}">${item.date} — [${item.remaining} left]</option>`;
                        });
                        capSelect.disabled = false;
                        capSelect.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                    } else {
                        capSelect.innerHTML = '<option value="">No availability found</option>';
                    }
                })
                .finally(() => loader.classList.add('hidden'));
        });
    </script>
</x-app-layout>