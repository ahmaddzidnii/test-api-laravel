<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'description',
        'path',
        'visibility',
        'width',
        'height',
        'mime_type',
        'size',
        'original_filename',
        'aspect_ratio',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }


    /**
     * Search scope
     */
    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        $searchTerm = '%' . strtolower($search) . '%';

        return $query->where(function ($q) use ($searchTerm) {
            $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                ->orWhereRaw('LOWER(description) LIKE ?', [$searchTerm]);
        });
    }

    /**
     * Sorting scope (whitelisted)
     */
    public function scopeSort($query, ?string $sortBy, ?string $direction)
    {
        $allowedSorts = ['title', 'created_at'];

        if (!in_array($sortBy, $allowedSorts)) {
            return $query;
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $direction);
    }
}
