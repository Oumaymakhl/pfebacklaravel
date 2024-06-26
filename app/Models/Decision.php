<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decision extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'likes', 'dislikes','company_id'];
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}

