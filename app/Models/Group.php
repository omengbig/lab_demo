<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['semester_id', 'name'];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }
}
