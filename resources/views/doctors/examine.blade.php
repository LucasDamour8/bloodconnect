<x-app-layout>
    <div class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-2xl shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <span class="font-black uppercase tracking-widest text-xs">Submission Failed</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 opacity-90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-orange-100 border-l-4 border-orange-500 text-orange-700 rounded-2xl shadow-sm">
                    <span class="font-black uppercase tracking-widest text-xs">System Error:</span>
                    <p class="text-xs">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-red-600 p-6 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-black uppercase tracking-tight">Medical Screening: {{ $appointment->user->first_name }}</h2>
                        <p class="text-sm opacity-80">Complete all 20 criteria for blood donation eligibility.</p>
                    </div>
                    <div class="bg-white/20 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/30 text-right">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-white/70">Acting Doctor</span>
                        <span class="text-sm font-bold uppercase">Dr. {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    </div>
                </div>

                <form action="{{ route('doctor.appointments.storeDonation', $appointment->id) }}" method="POST" class="p-8">
                    @csrf
                    
                    {{-- 1-6. Vital Signs --}}
                    <div class="mb-10">
                        <h3 class="font-black text-red-600 text-[11px] uppercase tracking-[0.2em] border-b border-red-100 pb-2 mb-6">Part I: Vital Signs & Physicals</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">1. Age (18-65)</label>
                                <input type="number" name="age" value="{{ old('age') }}" placeholder="Years" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">2. Weight (Min 50kg)</label>
                                <input type="number" name="weight" value="{{ old('weight') }}" placeholder="kg" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">3. BP (mmHg)</label>
                                <input type="text" name="blood_pressure" value="{{ old('blood_pressure') }}" placeholder="e.g. 120/80" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">4. Pulse (bpm)</label>
                                <input type="number" name="pulse_rate" value="{{ old('pulse_rate') }}" placeholder="60-100" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">5. Temp (°C)</label>
                                <input type="number" step="0.1" name="temperature" value="{{ old('temperature') }}" placeholder="36.5" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">6. Hemoglobin (g/dL)</label>
                                <input type="number" step="0.1" name="hemoglobin" value="{{ old('hemoglobin') }}" placeholder="Min 12.5" required class="w-full rounded-xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                    </div>

                    {{-- 7-15. Medical History --}}
                    <div class="mb-10 p-6 bg-gray-50 rounded-3xl border border-gray-100">
                        <h3 class="font-black text-gray-400 text-[11px] uppercase tracking-[0.2em] mb-6">Part II: Medical History & Grouping</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-700 uppercase mb-2">7-13. General Health Assessment</label>
                                    <textarea name="general_health" rows="5" class="w-full rounded-2xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500" placeholder="Notes on: General Health, Last donation, Minor/Major surgery, Jaundice/Hepatitis, Fainting, Medications, etc...">{{ old('general_health') }}</textarea>
                                    <p class="mt-2 text-[10px] text-gray-400 italic">Review criteria 7 through 13 as per clinical guidelines.</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">14. Blood Group</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach(['A', 'B', 'AB', 'O'] as $group)
                                            <label class="relative">
                                                <input type="radio" name="blood_group" value="{{ $group }}" class="peer sr-only" required>
                                                <div class="peer-checked:bg-red-600 peer-checked:text-white bg-white border border-gray-200 py-3 text-center rounded-xl cursor-pointer font-bold text-sm transition shadow-sm">
                                                    {{ $group }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase mb-2">15. Rhesus Factor</label>
                                    <div class="flex gap-4">
                                        <label class="flex-1 relative">
                                            <input type="radio" name="rhesus_factor" value="+" class="peer sr-only" required>
                                            <div class="peer-checked:bg-gray-900 peer-checked:text-white bg-white border border-gray-200 py-3 text-center rounded-xl cursor-pointer font-bold text-sm transition">
                                                Positive (+)
                                            </div>
                                        </label>
                                        <label class="flex-1 relative">
                                            <input type="radio" name="rhesus_factor" value="-" class="peer sr-only" required>
                                            <div class="peer-checked:bg-gray-900 peer-checked:text-white bg-white border border-gray-200 py-3 text-center rounded-xl cursor-pointer font-bold text-sm transition">
                                                Negative (-)
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 16-19. Lab Screening --}}
                    <div class="mb-10">
                        <h3 class="font-black text-red-600 text-[11px] uppercase tracking-[0.2em] border-b border-red-100 pb-2 mb-6">Part III: Serology (Lab Tests)</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach([
                                'hiv_test' => '16. HIV 1 & 2', 
                                'hep_b' => '17. HBsAg (Hep B)', 
                                'hep_c' => '18. HCV (Hep C)', 
                                'syphilis' => '19. VDRL (Syphilis)'
                            ] as $key => $label)
                            <div class="p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
                                <label class="block text-[9px] font-black text-gray-400 uppercase mb-3">{{ $label }}</label>
                                <select name="{{ $key }}" class="w-full rounded-lg border-gray-200 text-xs font-bold focus:ring-red-500">
                                    <option value="negative" class="text-green-600">NEGATIVE</option>
                                    <option value="positive" class="text-red-600">POSITIVE</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 20. Conclusion --}}
                    <div class="mt-6">
                        <label class="block text-[10px] font-black text-gray-700 uppercase mb-2">20. Medical Officer Conclusion</label>
                        <textarea name="conclusion" required class="w-full rounded-2xl border-gray-200 text-sm focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Final clinical recommendation regarding eligibility...">{{ old('conclusion') }}</textarea>
                    </div>

                    <div class="mt-10 flex flex-col md:flex-row gap-4">
                        <button type="submit" name="action" value="approved" class="flex-1 bg-green-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-green-700 transition shadow-lg shadow-green-100 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Approve & Register Donation
                        </button>
                        <button type="submit" name="action" value="rejected" class="flex-1 bg-gray-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-black transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reject Donor
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Footer --}}
            <div class="mt-8 text-center">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                    BloodConnect Medical Portal &bull; Confidentially Secured
                </p>
            </div>
        </div>
    </div>
</x-app-layout>