<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the projects that use this technology.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_technology');
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }
        $searchTerm = strtolower($search);
        return $query->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']);
    }

    /**
     * Sorting scope (whitelisted)
     */
    public function scopeSort($query, ?string $sortBy, ?string $direction)
    {
        $allowedSorts = ['name', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            return $query;
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $direction);
    }
}
