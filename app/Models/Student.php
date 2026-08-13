<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = ['group_id', 'matrix_number', 'name', 'email'];

    protected $appends = ['attendance_rate', 'is_at_risk'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the student's attendance rate as a percentage.
     *
     * @return float
     */
    public function getAttendanceRateAttribute()
    {
        $totalSessions = \App\Models\AttendanceSession::where('group_id', $this->group_id)->count();

        // Zero-session guard clause
        if ($totalSessions === 0) {
            return 100.0; // Default to 100% if no sessions have been held yet
        }

        // Optimize: use loaded relation if available to prevent N+1 queries
        if ($this->relationLoaded('attendances')) {
            $attended = $this->attendances->whereIn('status', ['present', 'late'])->count();
        } else {
            $attended = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        }

        // Attendance rate percentage formula
        $rate = ($attended / $totalSessions) * 100;

        // Rounding logic to preserve 1 decimal place
        return round($rate, 1);
    }

    /**
     * Check if the student has missed N consecutive sessions.
     *
     * @return bool
     */
    public function hasConsecutiveAbsences()
    {
        $limit = config('attendance.consecutive_absence_limit', 2);
        
        // Optimize: If relation is loaded, check memory collection
        if ($this->relationLoaded('attendances')) {
            $recent = $this->attendances->sortByDesc('attendance_session_id')->take($limit);
        } else {
            $recent = $this->attendances()->orderBy('attendance_session_id', 'desc')->take($limit)->get();
        }

        // If they haven't attended enough sessions to hit the limit, return false
        if ($recent->count() < $limit) {
            return false;
        }

        // Return true only if ALL of the last N sessions are marked 'absent'
        return $recent->every(function ($attendance) {
            return $attendance->status === 'absent';
        });
    }

    /**
     * Determine if the student is flagged as at-risk.
     *
     * @return bool
     */
    public function getIsAtRiskAttribute()
    {
        $rateThreshold = config('attendance.risk_threshold_percentage', 80.0);
        
        return $this->attendance_rate < $rateThreshold || $this->hasConsecutiveAbsences();
    }
}
