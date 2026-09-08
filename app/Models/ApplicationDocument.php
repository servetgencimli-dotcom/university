<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'admissions_application_id',
        'document_type',
        'file_path',
        'original_name',
        'file_size_kb',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(AdmissionsApplication::class, 'admissions_application_id');
    }
}
