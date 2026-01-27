<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTestimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'role',
        'avatar_url',
        'rating',
        'testimonial',
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the testimonial.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
