<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Group Attendance Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
        th { background-color: #f3f4f6; }
        .text-left { text-align: left; }
        .at-risk { color: #dc2626; font-weight: bold; }
        .tick { color: #10B981; font-weight: bold; }
        .cross { color: #EF4444; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Attendance Report: {{ $group->name }} ({{ $group->semester->name }})</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th class="text-left">Matrix Number</th>
                <th class="text-left">Name</th>
                <th>Rate</th>
                @foreach($sessions as $session)
                    <th>W{{ $session->week_number }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $student->matrix_number }}</td>
                    <td class="text-left {{ $student->is_at_risk ? 'at-risk' : '' }}">{{ $student->name }}</td>
                    <td class="{{ $student->is_at_risk ? 'at-risk' : '' }}">{{ $student->attendance_rate }}%</td>
                    @foreach($sessions as $session)
                        @php
                            $attendance = $student->attendances->firstWhere('attendance_session_id', $session->id);
                            $status = $attendance ? $attendance->status : 'absent';
                        @endphp
                        <td>
                            @if($status === 'present' || $status === 'late')
                                <span class="tick">&#10003;</span>
                            @else
                                <span class="cross">X</span> {{ $attendance && $attendance->reason ? "({$attendance->reason})" : '' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
