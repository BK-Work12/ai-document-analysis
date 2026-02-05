<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * DocumentTag Pivot Model
 * 
 * Relationship between documents and tags
 * Tracks auto-tagging with confidence and reasons
 */
class DocumentTag extends Pivot
{
    protected $table = 'document_tags';
    
    public $timestamps = true;

    protected $fillable = [
        'document_id',
        'tag_id',
        'reason',
        'confidence',
        'manual',
        'tagged_by',
        'tagged_at',
    ];

    protected function casts(): array
    {
        return [
            'manual' => 'boolean',
            'tagged_at' => 'datetime',
        ];
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function taggedBy()
    {
        return $this->belongsTo(User::class, 'tagged_by');
    }
}
