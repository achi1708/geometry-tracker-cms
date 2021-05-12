<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\App;

class Empresas extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'name',
        'logo'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_empresa', 'empresa_id', 'user_id')->withTimestamps();
    }

    public function apps()
    {
        return $this->belongsToMany(App::class, 'empresa_app', 'empresa_id', 'app_id')->withTimestamps();
    }
}
