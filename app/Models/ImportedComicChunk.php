<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedComicChunk extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'chunk_number',
        'last_job_uuid',
        'status',
        'started_at',
        'ended_at',
    ];
}
