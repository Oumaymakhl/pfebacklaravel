<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class admin extends  Authenticatable
{
    protected $fillable = [
        'nom',
        'prenom',
        'login',
        'password', 
        'email',
        
    ];
   
}
