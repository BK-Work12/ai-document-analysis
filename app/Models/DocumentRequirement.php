<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_type',
        'description',
        'required',
        'active',
        'sort_order',
    ];
}
