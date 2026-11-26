<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">Semesters</a> / 
            <a href="{{ route('semesters.show', $group->semester) }}" class="text-blue-500 hover:underline">{{ $group->semester->name }}</a> / 
            {{ $group->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md shadow-sm mb-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-lg shadow-sm gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Group Hub: {{ $group->name }}</h3>
                    <p class="text-sm text-gray-500">View weekly attendance grids and import new sheets.</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('groups.roster', $group) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150" title="Manage Name List">
                        Manage Roster
                    </a>
                    <a href="{{ route('groups.export.excel', $group) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" title="Export Excel">
                        Export CSV
                    </a>
                    <a href="{{ route('groups.export.pdf', $group) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" title="Export PDF">
                        Export PDF
                    </a>
                    <a href="{{ route('groups.import', $group) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Import Attendance PDF
                    </a>
                </div>
            </div>

            <!-- Analytics Chart -->
            @if($totalPresent > 0 || $totalAbsent > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 flex flex-col items-center">
                    <h3 class="text-lg font-medium mb-4">Overall Attendance Overview</h3>
                    <div class="w-64 h-64">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('attendanceChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Present', 'Absent'],
                            datasets: [{
                                data: [{{ $totalPresent }}, {{ $totalAbsent }}],
                                backgroundColor: ['#10B981', '#EF4444'],
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                });
            </script>
            @endif

            <!-- Master Attendance Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                        <h3 class="text-lg font-medium">Master Attendance Grid</h3>
                        
                        <!-- Filters -->
                        <div class="flex space-x-2">
                            <a href="{{ route('groups.show', ['group' => $group, 'filter' => request('filter') === 'at-risk' ? '' : 'at-risk', 'sort' => request('sort')]) }}" 
                               class="px-4 py-2 text-sm font-medium rounded-md border transition ease-in-out duration-150 {{ request('filter') === 'at-risk' ? 'bg-red-600 text-white border-red-600 hover:bg-red-700' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                {{ request('filter') === 'at-risk' ? 'Showing: At-Risk Only' : 'Show At-Risk Only' }}
                            </a>
                            <a href="{{ route('groups.show', ['group' => $group, 'filter' => request('filter') === 'good-standing' ? '' : 'good-standing', 'sort' => request('sort')]) }}" 
                               class="px-4 py-2 text-sm font-medium rounded-md border transition ease-in-out duration-150 {{ request('filter') === 'good-standing' ? 'bg-green-600 text-white border-green-600 hover:bg-green-700' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                {{ request('filter') === 'good-standing' ? 'Showing: Good Standing Only' : 'Show Good Standing' }}
                            </a>
                        </div>
                    </div>

                    <style>
                        .sticky-col { position: sticky; z-index: 10; }
                        thead .sticky-col { z-index: 20; }
                        .sticky-col-1 { left: 0px; width: 56px; min-width: 56px; max-width: 56px; }
                        .sticky-col-2 { left: 56px; width: 145px; min-width: 145px; max-width: 145px; }
                        .sticky-col-3 { left: 201px; width: 220px; min-width: 220px; max-width: 220px; }
                        .sticky-col-4 { left: 421px; width: 85px; min-width: 85px; max-width: 85px; }
                        .sticky-col-5 { left: 506px; width: 140px; min-width: 140px; max-width: 140px; }
                    </style>
                    <div class="overflow-x-auto pb-4">
                        <table class="min-w-full divide-y divide-gray-200 border" style="border-collapse: separate; border-spacing: 0;">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="sticky-col sticky-col-1 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">#</th>
                                    <th scope="col" class="sticky-col sticky-col-2 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Matrix Number</th>
                                    <th scope="col" class="sticky-col sticky-col-3 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                        <a href="{{ route('groups.show', ['group' => $group, 'filter' => request('filter'), 'sort' => request('sort') === 'name-asc' ? 'name-desc' : 'name-asc']) }}" class="flex items-center space-x-1 hover:text-gray-900 transition">
                                            <span>Name</span>
                                            @if(request('sort') === 'name-asc' || !request('sort'))
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th scope="col" class="sticky-col sticky-col-4 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Rate</th>
                                    <th scope="col" class="sticky-col sticky-col-5 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">Status</th>
                                    
                                    @foreach($sessions as $session)
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                            <div>Week {{ $session->week_number }}</div>
                                            @if($session->session_date)
                                                <div class="text-xs font-normal text-gray-400 mt-1">
                                                    {{ \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    @php $rowBg = $student->is_at_risk ? 'bg-red-50' : 'bg-green-50'; @endphp
                                    <tr class="{{ $rowBg }} hover:opacity-90 transition duration-150">
                                        <td class="sticky-col sticky-col-1 {{ $rowBg }} px-4 py-4 whitespace-nowrap text-sm text-gray-500 border-b border-gray-200">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="sticky-col sticky-col-2 {{ $rowBg }} px-4 py-4 whitespace-nowrap text-sm font-medium {{ $student->is_at_risk ? 'text-red-900' : 'text-green-900' }} border-b border-gray-200">
                                            {{ $student->matrix_number }}
                                        </td>
                                        <td class="sticky-col sticky-col-3 {{ $rowBg }} px-4 py-4 text-sm {{ $student->is_at_risk ? 'text-red-900' : 'text-green-900' }} border-b border-gray-200">
                                            {{ $student->name }}
                                        </td>
                                        <td class="sticky-col sticky-col-4 {{ $rowBg }} px-4 py-4 whitespace-nowrap text-sm font-bold {{ $student->is_at_risk ? 'text-red-900' : 'text-green-900' }} border-b border-gray-200">
                                            {{ $student->attendance_rate }}%
                                        </td>
                                        <td class="sticky-col sticky-col-5 {{ $rowBg }} px-4 py-4 whitespace-nowrap border-b border-gray-200">
                                            @if($student->is_at_risk)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                                    At-Risk
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                    Good Standing
                                                </span>
                                            @endif
                                        </td>
                                        
                                        @foreach($sessions as $session)
                                            @php
                                                $attendance = $student->attendances->firstWhere('attendance_session_id', $session->id);
                                                $status = $attendance ? $attendance->status : 'absent'; 
                                            @endphp
                                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                                @if($status === 'present' || $status === 'late')
                                                    <span class="text-green-500 font-bold text-lg" title="Present">✔️</span>
                                                @else
                                                    <span class="text-red-500 font-bold text-lg" title="Absent">❌</span>
                                                    <div class="mt-1 flex justify-center">
                                                        <form action="{{ route('attendances.reason', ['student' => $student->id, 'session' => $session->id]) }}" method="POST" class="flex items-center space-x-1">
                                                            @csrf
                                                            <input type="text" name="reason" value="{{ $attendance ? $attendance->reason : '' }}" class="text-xs border-gray-300 rounded w-20 px-1 py-0.5 focus:ring-indigo-500" placeholder="Reason">
                                                            <button type="submit" class="text-blue-500 hover:text-blue-700 bg-gray-100 border border-gray-200 rounded px-1" title="Save Reason">💾</button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + $sessions->count() }}" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No attendance data found in this group. Import a PDF to populate the grid.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
