<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        My Donation History
                    </h2>
                </div>

                @if($donations->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-gray-500">You haven't made any donations yet.</p>
                        <a href="{{ route('appointments.create') }}" class="mt-4 inline-block text-red-600 font-semibold hover:underline">
                            Schedule your first donation →
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Date</th>
                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Location</th>
                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Type</th>
                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Amount</th>
                                    <th class="py-3 px-4 text-sm font-semibold text-gray-600">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donations as $donation)
                                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                                        <td class="py-4 px-4 text-sm text-gray-700">
                                            {{ $donation->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-700">
                                            {{ $donation->location->name ?? 'N/A' }}
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $donation->donation_type)) }}
                                        </td>
                                        <td class="py-4 px-4 text-sm text-gray-700">
                                            {{ $donation->blood_amount }}ml
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                                Completed
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $donations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>