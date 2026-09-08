<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusBuilding extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'description',
        'floors_count',
        'capacity',
        'map_x',
        'map_y',
        'image',
        'facilities',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'map_x' => 'integer',
            'map_y' => 'integer',
            'floors_count' => 'integer',
            'capacity' => 'integer',
        ];
    }
}
