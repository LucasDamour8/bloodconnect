<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2 no-print">
                <i class="fa-solid fa-chart-line text-indigo-600"></i>
                {{ __('Performance & System Reports') }}
            </div>
            <div class="flex gap-2 no-print">
                <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Print Document
                </button>
                <a href="{{ route('doctor.reports.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="hidden print:block mb-8 text-center border-b pb-4">
        <h1 class="text-2xl font-bold uppercase">Blood Donation Medical Report</h1>
        <p class="text-sm text-gray-600 font-mono">Generated on: {{ now()->format('d M Y, H:i') }}</p>
        <p class="text-sm text-gray-600 font-mono">Provider: Dr. {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
    </div>

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 print:border-slate-300">
                <p class="text-sm font-medium text-gray-500 uppercase">Total Examinations</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total_exams'] }}</p>
                <div class="mt-2 text-xs text-green-600 font-bold no-print">
                    <i class="fa-solid fa-arrow-up"></i> Monthly Activity
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 print:border-slate-300">
                <p class="text-sm font-medium text-gray-500 uppercase">Donations Approved</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['approved'] }}</p>
                <div class="mt-2 text-xs text-gray-400">Successful collections</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 print:border-slate-300">
                <p class="text-sm font-medium text-gray-500 uppercase">Donor Satisfaction</p>
                <p class="text-3xl font-bold text-blue-600">98%</p>
                <div class="mt-2 text-xs text-blue-400 font-bold">Based on feedback</div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 no-print">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Donation Activity (Last 6 Months)</h3>
            <div class="h-[400px]">
                <canvas id="donationChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-slate-50 print:bg-white">
                <h3 class="font-bold text-gray-800 text-sm uppercase">Center Activity Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 print:bg-white border-b">
                            <th class="p-4 font-bold">Center Name</th>
                            <th class="p-4 font-bold">Assigned Doctors</th>
                            <th class="p-4 font-bold text-center">Total</th>
                            <th class="p-4 font-bold text-green-600 text-center">Completed</th>
                            <th class="p-4 font-bold text-red-500 text-center">Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignedLocations as $center)
                        <tr class="border-b">
                            <td class="p-4 font-medium">{{ $center->name }}</td>
                            <td class="p-4 text-gray-500 text-xs">
                                {{ $center->doctors->pluck('last_name')->implode(', ') }}
                            </td>
                            <td class="p-4 text-center">{{ $center->donations->count() }}</td>
                            <td class="p-4 text-center font-bold text-green-600">
                                {{ $center->donations->where('status', 'completed')->count() }}
                            </td>
                            <td class="p-4 text-center font-bold text-red-400">
                                {{ $center->donations->where('status', 'cancelled')->count() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print, nav, aside, button, .sidebar {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .bg-white {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('donationChart').getContext('2d');
            
            // Using labels and values sent from the Controller's reports() method
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        label: 'Units Collected',
                        data: {!! json_encode($values) !!},
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#EF4444'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>