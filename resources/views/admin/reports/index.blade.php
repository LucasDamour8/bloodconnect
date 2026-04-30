<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10 px-4">
        <div class="max-w-7xl mx-auto">
            
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 uppercase tracking-tight">System Reports</h1>
                    <p class="text-gray-500 mt-1 font-medium text-sm">Live metrics for {{ $monthName }}.</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="window.print()" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                        <span>🖨️</span> Print Report
                    </button>
                    {{-- CSV Export Trigger --}}
                    <button id="exportCsvBtn" class="bg-gray-900 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-800 transition shadow-lg flex items-center gap-2">
                        <span>📥</span> Export CSV
                    </button>
                </div>
            </div>

            {{-- Top Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                @foreach([
                    ['Total Donors', $stats['donors'], 'bg-green-600', '📍'],
                    ['Total Donations', $stats['completed_donations'], 'bg-red-600', '❤️'],
                    ['Locations', $stats['locations'], 'bg-blue-600', '🏢'],
                    ['Pending Appts', $stats['pending_appointments'], 'bg-orange-500', '🕒']
                ] as $item)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $item[0] }}</p>
                    <h3 class="text-3xl font-black mt-1 text-gray-900">{{ $item[1] }}</h3>
                    <div class="mt-2 text-[10px] font-bold {{ str_replace('bg-', 'text-', $item[2]) }} flex items-center gap-1">
                        <span class="opacity-70">{{ $item[3] }}</span> Verified System Data
                    </div>
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Column --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Monthly Trends Bar Chart --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Daily Volume ({{ $monthName }})</h2>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">Showing: {{ $selectedStatus }}</p>
                            </div>
                            
                            {{-- Status Filter Dropdown --}}
                            <form action="{{ route('admin.reports') }}" method="GET" id="chartFilterForm">
                                <select name="chart_status" onchange="document.getElementById('chartFilterForm').submit()" 
                                        class="rounded-lg border-gray-200 text-[10px] font-black uppercase tracking-wider focus:ring-green-500 focus:border-green-500 bg-gray-50 text-gray-600 cursor-pointer">
                                    <option value="completed" {{ $selectedStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="approved" {{ $selectedStatus == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="cancelled" {{ $selectedStatus == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="scheduled" {{ $selectedStatus == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                            </form>
                        </div>
                        <div style="height: 320px; position: relative;">
                            <canvas id="donationChart"></canvas>
                        </div>
                    </div>

                    {{-- Location Table (Used for CSV Export) --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                            <h2 class="text-lg font-bold text-gray-900">Location Performance</h2>
                            <span class="text-xs font-bold text-green-600">Top 5 Centers</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table id="reportsTable" class="w-full text-left">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">Center Name</th>
                                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider text-right">Completed Units</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($locations as $loc)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 font-bold text-gray-800">{{ $loc->name }}</td>
                                        <td class="px-6 py-4 text-gray-600 text-right font-mono font-bold">{{ $loc->donations_count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Side Column --}}
                <div class="space-y-6">
                    {{-- Blood Type Distribution --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="text-sm font-black text-gray-900 uppercase mb-6 tracking-widest">Inventory Mix</h2>
                        <div class="space-y-5">
                            @foreach($bloodTypes as $bt)
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-2">
                                    <span class="text-gray-700">Type {{ $bt['type'] }}</span>
                                    <span class="text-gray-400">{{ $bt['percentage'] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $bt['color'] }} h-full" style="width: {{ $bt['percentage'] }}"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Recent System Activity Feed --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                        <h2 class="text-sm font-black text-gray-900 uppercase mb-4 tracking-widest">System Feed</h2>
                        <div class="space-y-6">
                            @foreach($recentActivity as $activity)
                            <div class="flex gap-4 relative">
                                <div class="w-2 h-2 rounded-full {{ $activity['icon'] }} mt-1.5 shrink-0 shadow-sm"></div>
                                <div>
                                    <p class="text-xs font-bold text-gray-800 leading-tight">{{ $activity['title'] }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase font-medium">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Chart Configuration
            const ctx = document.getElementById('donationChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: '{{ ucfirst($selectedStatus) }} Appointments',
                        data: @json($chartValues),
                        backgroundColor: '#16a34a', // Professional Green
                        borderRadius: 4,
                        hoverBackgroundColor: '#15803d',
                        barThickness: 'flex'
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
                            ticks: { stepSize: 1, font: { size: 10 } },
                            grid: { color: '#f3f4f6' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { 
                                font: { size: 8, weight: 'bold' },
                                autoSkip: true,
                                maxRotation: 0
                            }
                        }
                    }
                }
            });

            // 2. CSV Export Logic
            document.getElementById('exportCsvBtn').addEventListener('click', function() {
                let csv = 'Location Name,Completed Units\n';
                const rows = document.querySelectorAll('#reportsTable tbody tr');
                
                rows.forEach(row => {
                    const cols = row.querySelectorAll('td');
                    if(cols.length >= 2) {
                        csv += `"${cols[0].innerText.trim()}","${cols[1].innerText.trim()}"\n`;
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.setAttribute('href', url);
                link.setAttribute('download', 'Monthly_Donation_Report_{{ now()->format('Y-m') }}.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
</x-app-layout>