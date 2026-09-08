<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchPaper extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'author_id',
        'co_authors',
        'category',
        'abstract',
        'journal_name',
        'doi',
        'publication_date',
        'citations_count',
        'download_url',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'citations_count' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
