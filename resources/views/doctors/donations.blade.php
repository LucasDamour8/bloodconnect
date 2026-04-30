<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Performed Donations') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F7F7F7] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Provider Stats Row --}}
            <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Exams Conducted</p>
                    <p class="text-2xl font-bold text-[#2A3F54] mt-1">{{ $donations->total() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between items-md-center gap-4 mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Finalized Donation Records</h3>
                            <p class="text-sm text-gray-500">Historical data of all donations verified by you.</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                                <i class="fa-solid fa-file-pdf"></i> EXPORT LIST
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-xs font-semibold text-gray-400 uppercase px-4">
                                    <th class="px-4 py-3">Donor Information</th>
                                    <th class="px-4 py-3">Medical Vitals</th>
                                    <th class="px-4 py-3">Location</th>
                                    <th class="px-4 py-3">Date & Time</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Records</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donations as $donation)
                                <tr class="bg-white hover:shadow-md transition-shadow duration-200 group">
                                    {{-- Donor Info --}}
                                    <td class="px-4 py-4 border-y border-l border-gray-50 rounded-l-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 bg-red-50 rounded-full flex items-center justify-center text-red-600 font-bold text-xs border border-red-100">
                                                {{ $donation->blood_type }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $donation->user->first_name }} {{ $donation->user->last_name }}</div>
                                                <div class="text-[10px] text-gray-400 uppercase font-medium">ID: #DON-{{ $donation->id }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Vitals (Doctor Specific Info) --}}
                                    <td class="px-4 py-4 border-y border-gray-50">
                                        <div class="text-xs text-gray-600 space-y-1">
                                            <p><span class="text-gray-400">BP:</span> {{ $donation->blood_pressure }}</p>
                                            <p><span class="text-gray-400">Hgb:</span> {{ $donation->hemoglobin }} g/dL</p>
                                        </div>
                                    </td>

                                    {{-- Location --}}
                                    <td class="px-4 py-4 border-y border-gray-50">
                                        <div class="flex items-center gap-2 text-sm text-gray-700">
                                            <i class="fa-solid fa-location-dot text-gray-300 text-xs"></i>
                                            {{ $donation->location->name }}
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-4 py-4 border-y border-gray-50">
                                        <div class="text-sm font-medium text-gray-700">
                                            {{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            {{ \Carbon\Carbon::parse($donation->donation_date)->format('h:i A') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-4 border-y border-gray-50">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            <i class="fa-solid fa-check-double mr-1 text-[10px]"></i> Completed
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-4 py-4 border-y border-r border-gray-50 rounded-r-xl text-right">
                                        <a href="{{ route('doctor.appointments.viewResults', $donation->appointment_id) }}" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-[#2A3F54] hover:text-white transition-all">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-20 text-center">
                                        <div class="max-w-xs mx-auto">
                                            <i class="fa-solid fa-notes-medical text-gray-200 text-5xl mb-4"></i>
                                            <h4 class="text-gray-900 font-bold">No Completed Records</h4>
                                            <p class="text-gray-500 text-sm mt-1">Donations you finalize will appear here for your records.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8">
                        {{ $donations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>