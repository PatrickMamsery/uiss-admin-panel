<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class MemberDetail extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'user_id',
        'reg_no',
        'area_of_interest',
        'university_id',
        'college_id',
        'department_id',
        'degree_programme_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function university()
    {
        return $this->belongsTo(University::class, 'university_id', 'id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function degreeProgramme()
    {
        return $this->belongsTo(DegreeProgramme::class, 'degree_programme_id', 'id');
    }
}
