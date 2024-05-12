<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Comic extends Model
{
    use HasFactory;

    protected $fillable = [
        'marvel_id',
        'title',
        'details_url',
        'thumbnail',
        'released_on',
    ];
}
