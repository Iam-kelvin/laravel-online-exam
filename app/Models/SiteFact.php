<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteFact extends Model
{
    use HasFactory;

    protected $fillable = ['kind', 'title', 'body', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];
}
