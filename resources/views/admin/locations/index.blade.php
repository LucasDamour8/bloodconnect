<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-7xl mx-auto">
            
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 uppercase tracking-tight">Blood Collection Centers</h1>
                    <p class="text-gray-500 mt-1 font-medium text-sm">Manage all physical locations and hospital partners.</p>
                </div>
                {{-- UPDATED: route name to admin.centers.create --}}
                <a href="{{ route('admin.centers.create') }}" class="bg-red-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-red-700 transition shadow-lg shadow-red-100 flex items-center gap-2">
                    <span>+</span> Add New Location
                </a>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-xs font-bold uppercase tracking-wide">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Location Name</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">City</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Availability</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Walk-ins</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($locations as $location)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-lg">📍</div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $location->name }}</p>
                                            <p class="text-[10px] text-gray-400 font-medium">{{ $location->address }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-600">
                                    {{ $location->city }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $availColors = [
                                            'high' => 'bg-green-100 text-green-600',
                                            'medium' => 'bg-blue-100 text-blue-600',
                                            'low' => 'bg-orange-100 text-orange-600',
                                        ][$location->availability] ?? 'bg-gray-100 text-gray-600';
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase {{ $availColors }}">
                                        {{ $location->availability }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold {{ $location->walk_ins ? 'text-green-500' : 'text-gray-300' }}">
                                    {{ $location->walk_ins ? 'YES' : 'NO' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase {{ $location->is_active ? 'text-green-600' : 'text-red-400' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $location->is_active ? 'bg-green-600' : 'bg-red-400' }}"></span>
                                        {{ $location->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        {{-- UPDATED: route name to admin.centers.edit --}}
                                        <a href="{{ route('admin.centers.edit', $location->id) }}" class="p-2 text-gray-400 hover:text-blue-600 transition hover:bg-blue-50 rounded-lg">
                                            ✏️
                                        </a>
                                        {{-- UPDATED: route name to admin.centers.destroy --}}
                                        <form action="{{ route('admin.centers.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Delete this location?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition hover:bg-red-50 rounded-lg">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-gray-400 font-bold text-sm">No locations found. Start by adding one!</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($locations->hasPages())
                <div class="p-6 border-t border-gray-50 bg-gray-50/30">
                    {{ $locations->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>