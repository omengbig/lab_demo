<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceSessionFactory> */
    use HasFactory;

    protected $fillable = ['group_id', 'created_by_user_id', 'week_number', 'session_date'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
