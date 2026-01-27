<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'image_url',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user associated with the avatar image.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'avatar_image_id', 'key');
    }
}
