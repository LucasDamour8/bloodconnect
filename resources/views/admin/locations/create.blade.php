<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-3xl mx-auto">
            
            {{-- Navigation Back --}}
            <div class="mb-6">
                <a href="{{ route('admin.centers.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-red-600 transition-colors group">
                    <span class="transform group-hover:-translate-x-1 transition-transform">←</span> 
                    BACK TO LOCATIONS
                </a>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="p-8 border-b border-gray-50 bg-white">
                    <h1 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Add New Location</h1>
                    <p class="text-gray-500 text-sm mt-1 font-medium">Register a center and assign multiple doctors.</p>
                </div>

                {{-- Form --}}
                <form action="{{ route('admin.centers.store') }}" method="POST" class="p-8 space-y-6">
                    @csrf

                    {{-- Validation Errors Display --}}
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 mb-6 rounded-r-xl">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <p class="font-black text-xs uppercase tracking-widest">Please fix these errors:</p>
                            </div>
                            <ul class="list-disc list-inside text-xs space-y-1 ml-6">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Location Name --}}
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Location Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required 
                                   class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none"
                                   placeholder="e.g. MUHIMA CENTER">
                        </div>

                        {{-- Physical Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Physical Address</label>
                            <input type="text" name="address" value="{{ old('address') }}" required 
                                   class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none"
                                   placeholder="e.g. Nyarugenge, KN 4 St">
                        </div>

                        {{-- City/District --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">City / District</label>
                            <input type="text" name="city" value="{{ old('city') }}" required 
                                   class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none"
                                   placeholder="KIGALI-RWANDA">
                        </div>

                        {{-- Contact Phone --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Contact Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" 
                                   class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none"
                                   placeholder="+250783722606">
                        </div>

                        {{-- Availability Level --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Availability Status</label>
                            <select name="availability" required 
                                    class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none">
                                <option value="" disabled {{ old('availability') ? '' : 'selected' }}>Select Level</option>
                                <option value="high" {{ old('availability') == 'high' ? 'selected' : '' }}>High Availability</option>
                                <option value="medium" {{ old('availability') == 'medium' ? 'selected' : '' }}>Medium Availability</option>
                                <option value="low" {{ old('availability') == 'low' ? 'selected' : '' }}>Low Availability</option>
                            </select>
                        </div>

                        {{-- Operating Hours --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Operating Hours</label>
                            <input type="text" name="hours" value="{{ old('hours') }}" 
                                   class="w-full bg-gray-50 border-gray-100 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all outline-none"
                                   placeholder="6:00 TO 20:00">
                        </div>

                        {{-- Assigned Doctors (Checkbox Grid) --}}
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Assigned Doctors</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                @foreach($doctors as $doctor)
                                    <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-100 cursor-pointer hover:border-red-200 hover:bg-red-50/10 transition-all group">
                                        <input type="checkbox" name="doctor_ids[]" value="{{ $doctor->id }}" 
                                            {{ (is_array(old('doctor_ids')) && in_array($doctor->id, old('doctor_ids'))) ? 'checked' : '' }}
                                            class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500 transition-all">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-700 uppercase tracking-tight group-hover:text-red-600 transition-colors">
                                                Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                            </span>
                                            <span class="text-[10px] text-gray-400 font-medium">{{ $doctor->email }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[9px] text-gray-400 mt-2 italic">* Select one or more doctors to manage this center.</p>
                        </div>

                        {{-- Capacity & Date Range Section --}}
                        <div class="md:col-span-2 mt-4">
                            <h2 class="text-xs font-black text-red-600 uppercase tracking-widest mb-4 border-b border-red-50 pb-2">Capacity & Date Range</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-red-50/30 p-5 rounded-2xl border border-red-50">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Active From</label>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}" required 
                                           class="w-full bg-white border-gray-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500/20 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Active Until</label>
                                    <input type="date" name="end_date" value="{{ old('end_date') }}" required 
                                           class="w-full bg-white border-gray-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500/20 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Max Donors / Day</label>
                                    <input type="number" name="max_donors" value="{{ old('max_donors', 50) }}" required min="1" 
                                           class="w-full bg-white border-gray-100 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500/20 outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Walk-ins Checkbox --}}
                        <div class="md:col-span-2 flex items-center gap-3 p-2">
                            <input type="checkbox" name="walk_ins" id="walk_ins" value="1" {{ old('walk_ins') ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500 transition-all">
                            <label for="walk_ins" class="text-xs font-black text-gray-600 uppercase tracking-tight cursor-pointer">Allow Walk-ins</label>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="pt-8 flex items-center justify-end gap-4 border-t border-gray-50">
                        <a href="{{ route('admin.centers.index') }}" class="text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-red-600 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition shadow-lg shadow-red-200 active:transform active:scale-95">
                            Save & Initialize Center
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>