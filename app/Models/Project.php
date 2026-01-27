<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'about',
        'slug',
        'duration',
        'launch_year',
        'demo_url',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the technologies for the project.
     */
    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    /**
     * Get the key features for the project.
     */
    public function keyFeatures()
    {
        return $this->hasMany(KeyFeatureProject::class);
    }

    /**
     * Get the challenges for the project.
     */
    public function challenges()
    {
        return $this->hasMany(ChallengeProject::class);
    }

    /**
     * Get the results for the project.
     */
    public function results()
    {
        return $this->hasMany(ResultsProject::class);
    }

    /**
     * Get the images for the project.
     */
    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }

    /**
     * Get the testimonials for the project.
     */
    public function testimonials()
    {
        return $this->hasMany(ProjectTestimonial::class);
    }
}
