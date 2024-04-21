<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = 
    ['nom',
      'subdomaine',
       'logo',
       'adresse',
       'admin_id',
     
    ]; 
    public function admins()
    {
        return $this->hasOne(admin::class);
    }
    

}
