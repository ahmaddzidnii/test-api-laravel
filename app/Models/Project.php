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
        'user_id',
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
     * Get the thumbnail image for the project.
     */
    public function thumbnail()
    {
        return $this->hasOne(ProjectImage::class)
            ->where('is_primary', true)
            ->where('is_used', true);
    }

    /**
     * Get the testimonials for the project.
     */
    public function testimonials()
    {
        return $this->hasMany(ProjectTestimonial::class);
    }

    /**
     * Get the user who created the project.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Search scope - Database agnostic
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        $searchTerm = '%' . strtolower($search) . '%';

        return $query->where(function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm])
                ->orWhere(function ($subQ) use ($searchTerm) {
                    $subQ->whereNotNull('about')
                        ->whereRaw('LOWER(about) LIKE ?', [$searchTerm]);
                });
        });
    }

    /**
     * Sorting scope (whitelisted)
     */
    public function scopeSort($query, ?string $sortBy, ?string $direction)
    {
        $allowedSorts = ['title', 'launch_year', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            return $query;
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $direction);
    }
}
