<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight no-print">
            {{ __('Medical Record: ') }} {{ $donation->user->first_name }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- The printable area --}}
            <div id="medical-report" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                
                {{-- Header --}}
                <div class="bg-slate-900 p-8 text-white print:bg-slate-900 print:text-white">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-6">
                            {{-- Profile Photo --}}
                            <div class="relative">
                                @if($donation->user->profile_photo)
                                    <img src="{{ asset('storage/' . $donation->user->profile_photo) }}" 
                                         class="w-24 h-24 rounded-2xl object-cover border-2 border-indigo-500 shadow-lg"
                                         alt="Donor Photo">
                                @else
                                    <div class="w-24 h-24 rounded-2xl bg-slate-800 border-2 border-slate-700 flex items-center justify-center shadow-lg">
                                        <span class="text-3xl font-black text-slate-500">{{ substr($donation->user->first_name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <p class="text-indigo-400 text-[10px] font-black uppercase tracking-widest mb-1">Official Donation Record</p>
                                <h1 class="text-3xl font-black uppercase tracking-tight">
                                    {{ $donation->user->first_name }} {{ $donation->user->last_name }}
                                </h1>
                                <p class="text-slate-400 text-sm font-medium mt-1">Donor ID: #DN-00{{ $donation->user->id }}</p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="bg-emerald-500/20 text-emerald-400 px-4 py-2 rounded-xl text-[10px] font-black uppercase border border-emerald-500/30">
                                Status: {{ strtoupper($donation->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-8 space-y-8">
                    {{-- 1. Appointment Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date of Donation</span>
                            <span class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Medical Center</span>
                            <span class="text-sm font-bold text-slate-800">{{ $donation->location->name }}</span>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Blood Group</span>
                            <span class="text-sm font-black text-red-600">{{ $donation->blood_type }}</span>
                        </div>
                    </div>

                    {{-- 2. Vital Signs --}}
                    <div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-indigo-500 rounded-full"></span> Vital Signs
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="border border-slate-100 p-4 rounded-2xl text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Hemoglobin</p>
                                <p class="text-lg font-black text-slate-900">{{ $donation->hemoglobin }} <small class="text-[10px]">g/dL</small></p>
                            </div>
                            <div class="border border-slate-100 p-4 rounded-2xl text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">BP</p>
                                <p class="text-lg font-black text-slate-900">{{ $donation->blood_pressure }}</p>
                            </div>
                            <div class="border border-slate-100 p-4 rounded-2xl text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Temp</p>
                                <p class="text-lg font-black text-slate-900">{{ $donation->temperature }} °C</p>
                            </div>
                            <div class="border border-slate-100 p-4 rounded-2xl text-center">
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Weight</p>
                                <p class="text-lg font-black text-slate-900">{{ $donation->weight }} kg</p>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Screening Results --}}
                    <div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span> Screening Results
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-3 rounded-xl border {{ $donation->hiv_test == 'negative' ? 'bg-green-50' : 'bg-red-50' }}">
                                <p class="text-[9px] font-bold uppercase">HIV Test</p>
                                <p class="font-bold text-sm">{{ ucfirst($donation->hiv_test) }}</p>
                            </div>
                            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50">
                                <p class="text-[9px] font-bold uppercase">Hep B</p>
                                <p class="font-bold text-sm">{{ ucfirst($donation->hep_b) }}</p>
                            </div>
                            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50">
                                <p class="text-[9px] font-bold uppercase">Hep C</p>
                                <p class="font-bold text-sm">{{ ucfirst($donation->hep_c) }}</p>
                            </div>
                            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50">
                                <p class="text-[9px] font-bold uppercase">Syphilis</p>
                                <p class="font-bold text-sm">{{ ucfirst($donation->syphilis) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Conclusion --}}
                    <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100">
                        <h3 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-2">Medical Conclusion</h3>
                        <p class="text-slate-700 font-medium leading-relaxed italic">
                            "{{ $donation->conclusion }}"
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-6 border-t border-slate-100 no-print">
                        <a href="{{ route('doctor.appointments') }}" class="text-slate-400 hover:text-slate-900 text-[10px] font-black uppercase tracking-widest transition">
                            &larr; Return to Queue
                        </a>
                        <button onclick="generatePDF()" class="bg-indigo-600 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-lg shadow-indigo-100">
                            Print / Save as PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function generatePDF() {
            const originalTitle = document.title;
            const donorName = "{{ $donation->user->first_name }}_{{ $donation->user->last_name }}";
            
            document.title = donorName + "_Medical_Report";
            window.print();
            
            setTimeout(() => {
                document.title = originalTitle;
            }, 1000);
        }
    </script>

    <style>
        @media print {
            .no-print, nav, aside, header, button, .alert { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .py-12 { padding: 0 !important; }
            #medical-report {
                display: block !important;
                width: 100% !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</x-app-layout>