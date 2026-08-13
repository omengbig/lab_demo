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
                <div>
                    <a href="{{ route('groups.import', $group) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Import Attendance PDF
                    </a>
                </div>
            </div>

            <!-- Master Attendance Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Master Attendance Grid</h3>

                    <div class="overflow-x-auto pb-4">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrix Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    
                                    @foreach($sessions as $session)
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}">
                                            Week {{ $session->week_number }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    <tr class="{{ $student->is_at_risk ? 'bg-red-50' : 'bg-green-50' }} hover:opacity-90 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $student->is_at_risk ? 'text-red-900' : 'text-green-900' }}">
                                            {{ $student->matrix_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $student->is_at_risk ? 'text-red-900' : 'text-green-900' }}">
                                            {{ $student->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $student->is_at_risk ? 'text-red-900' : 'text-green-900' }}">
                                            {{ $student->attendance_rate }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 4 + $sessions->count() }}" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
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
