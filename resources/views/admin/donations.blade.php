<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Donation Logs</h1>
                <p class="text-gray-500 mt-1">Complete history of all blood donations across all centers.</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
                <span class="text-xs font-bold text-green-600 uppercase tracking-widest">
                    Total Records: {{ $donations->total() }}
                </span>
            </div>
        </div>

        {{-- Main Table Section --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Donor</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Blood Type</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Location</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Date</th>
                            <th class="p-5 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($donations as $donation)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-xs">
                                        {{ substr($donation->user->first_name, 0, 1) }}{{ substr($donation->user->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $donation->user->first_name }} {{ $donation->user->last_name }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $donation->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-5">
                                <span class="px-2.5 py-1 rounded-md bg-red-50 text-red-600 text-xs font-black">
                                    {{ $donation->user->blood_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-5 text-sm text-gray-600 font-medium">
                                {{ $donation->location->name ?? 'Unknown Center' }}
                            </td>
                            <td class="p-5">
                                <p class="text-sm text-gray-900 font-bold">{{ $donation->created_at->format('M d, Y') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $donation->created_at->format('h:i A') }}</p>
                            </td>
                            <td class="p-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase 
                                    {{ $donation->status === 'completed' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                                    {{ $donation->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-20 text-center text-gray-400 italic">
                                No donation records found in the system.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if($donations->hasPages())
            <div class="p-6 border-t border-gray-50 bg-gray-50/30">
                {{ $donations->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>