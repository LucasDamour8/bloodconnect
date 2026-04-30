{{-- resources/views/eligibility.blade.php --}}
<div class="max-w-4xl mx-auto">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900">Eligibility Check</h1>
        <p class="text-gray-500 mt-2">Quickly find out if you are ready to donate today.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('eligibility.check') }}" method="POST" class="p-8 md:p-12 space-y-8">
            @csrf
            
            <div class="grid gap-8 md:grid-cols-2">
                {{-- Basic Health --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600 text-sm">1</span>
                        Basic Health
                    </h3>
                    
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition">
                            <input type="checkbox" name="feeling_well" value="1" class="rounded text-red-600 focus:ring-red-500">
                            <span class="text-sm font-medium text-gray-700">I am feeling well and healthy today</span>
                        </label>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Weight (kg)</label>
                                <input type="number" name="weight" placeholder="e.g. 65" class="w-full rounded-xl border-gray-100 bg-gray-50 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Age</label>
                                <input type="number" name="age" placeholder="e.g. 25" class="w-full rounded-xl border-gray-100 bg-gray-50 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent History --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100 text-red-600 text-sm">2</span>
                        Recent History
                    </h3>
                    
                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Any recent illness or fever?</label>
                        <select name="recent_illness" class="w-full rounded-xl border-gray-100 bg-gray-50 focus:ring-red-500">
                            <option value="no">No, none</option>
                            <option value="yes">Yes, within last 2 weeks</option>
                        </select>

                        <label class="block text-sm font-medium text-gray-700">Have you traveled abroad recently?</label>
                        <select name="recent_travel" class="w-full rounded-xl border-gray-100 bg-gray-50 focus:ring-red-500">
                            <option value="no">No</option>
                            <option value="yes">Yes, in the last 6 months</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-400 max-w-md">
                    Note: This is a preliminary check. A final medical screening will be conducted by professionals at the donation center.
                </p>
                <button type="submit" class="w-full md:w-auto px-8 py-4 bg-red-600 text-white font-bold rounded-2xl hover:bg-red-700 transition shadow-lg shadow-red-100">
                    Check My Eligibility
                </button>
            </div>
        </form>
    </div>
</div>