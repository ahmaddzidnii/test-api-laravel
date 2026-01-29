<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'file_name',
        'file_type',
        'file_size',
        'project_id',
        'is_primary',
        'is_used',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_used' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the image.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
