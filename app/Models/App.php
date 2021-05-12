<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresas;

class App extends Model
{
    use HasFactory;

    protected $table = 'app';

    protected $fillable = [
        'name',
        'logo'
    ];

    public function empresas()
    {
        return $this->belongsToMany(Empresas::class, 'empresa_app', 'app_id', 'empresa_id')->withTimestamps();
    }
}
