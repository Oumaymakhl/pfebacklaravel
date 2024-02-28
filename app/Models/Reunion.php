<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reunion extends Model
{
    use HasFactory;
    protected $fillable = ['id_admin','titre', 'description', 'date','statut'];

    public function admin()
    {
        return $this->belongsTo(admin::class);
    }
}
