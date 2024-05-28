<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;
    protected $fillable = ['id_admin','titre', 'description', 'date'];

    public function admin()
    {
        return $this->belongsTo(admin::class);
    }
  
    public function users()
    {
        return $this->belongsToMany(User::class, 'presence')->withPivot('status', 'raison');
    }

}
