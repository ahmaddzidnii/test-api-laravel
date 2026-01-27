<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'challenge',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the challenge.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
