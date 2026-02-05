<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tag Model
 * 
 * Tags for classifying and categorizing documents
 * Examples: Financial, Legal, Risk, Verified, etc.
 */
class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'category',
        'order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    /**
     * Get documents with this tag
     */
    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_tags')
            ->withPivot('reason', 'confidence', 'manual', 'tagged_by', 'tagged_at')
            ->withTimestamps();
    }
}
