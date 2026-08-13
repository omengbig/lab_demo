<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Weekly Attendance Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Master Attendance Grid</h3>

                    <div class="overflow-x-auto pb-4">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matrix Number</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    
                                    @foreach($sessions as $session)
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" title="{{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}">
                                            Week {{ $session->week_number }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $student->matrix_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $student->name }}
                                        </td>
                                        
                                        @foreach($sessions as $session)
                                            @php
                                                $attendance = $student->attendances->firstWhere('attendance_session_id', $session->id);
                                                // Default to absent if missing, per user specs
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
                                        <td colspan="{{ 2 + $sessions->count() }}" class="px-6 py-12 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No attendance data found. Import a PDF to populate the grid.
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
