<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 uppercase tracking-tight">Update Center</h1>
                <a href="{{ route('admin.centers.index') }}" class="text-xs font-bold text-gray-400">&larr; BACK</a>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200">
                    <ul class="text-xs font-bold uppercase tracking-wide list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.centers.update', $location->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="md:col-span-2 grid md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Center Name</label>
                                <input type="text" name="name" value="{{ old('name', $location->name) }}" class="w-full bg-gray-50 border-none rounded-xl p-4 text-sm font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Max Donors</label>
                                <input type="number" name="max_donors" value="{{ old('max_donors', $location->max_donors) }}" class="w-full bg-gray-50 border-none rounded-xl p-4 text-sm font-bold">
                            </div>
                        </div>

                        <div class="md:col-span-2 grid md:grid-cols-2 gap-4 bg-red-50/30 p-4 rounded-2xl">
                            <div>
                                <label class="block text-[10px] font-black text-red-400 uppercase mb-2">Active From</label>
                                <input type="date" name="active_from" value="{{ old('active_from', $location->active_from) }}" class="w-full bg-white border-none rounded-xl p-4 text-sm font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-red-400 uppercase mb-2">Active Until</label>
                                <input type="date" name="active_until" value="{{ old('active_until', $location->active_until) }}" class="w-full bg-white border-none rounded-xl p-4 text-sm font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">City</label>
                            <input type="text" name="city" value="{{ old('city', $location->city) }}" class="w-full bg-gray-50 border-none rounded-xl p-4 text-sm font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Address</label>
                            <input type="text" name="address" value="{{ old('address', $location->address) }}" class="w-full bg-gray-50 border-none rounded-xl p-4 text-sm font-bold">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase mb-2">Urgency Level</label>
                            <select name="availability" class="w-full bg-gray-50 border-none rounded-xl p-4 text-sm font-bold">
                                <option value="low" {{ $location->availability == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $location->availability == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $location->availability == 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 flex items-center gap-8 py-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="walk_ins" value="1" {{ $location->walk_ins ? 'checked' : '' }} class="rounded text-red-600">
                                <span class="text-xs font-bold text-gray-600 uppercase">Allow Walk-ins</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }} class="rounded text-green-600">
                                <span class="text-xs font-bold text-gray-600 uppercase">Active</span>
                            </label>
                        </div>

                    </div>

                    <button type="submit" class="mt-8 w-full py-4 bg-gray-900 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl">
                        Update Center
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>