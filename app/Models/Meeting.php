<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = ['id_admin', 'titre', 'description', 'date', 'link'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'meeting_user');
    }
}
