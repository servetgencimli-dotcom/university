<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Faculty extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'dean_name',
        'description',
        'icon',
        'image',
        'color',
        'established_year',
        'students_count',
        'professors_count',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function programs(): HasManyThrough
    {
        return $this->hasManyThrough(Program::class, Department::class);
    }

    public function professors(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'teacher');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class)->whereIn('role', ['student', 'master_student']);
    }
}
