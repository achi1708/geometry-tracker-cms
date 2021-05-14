<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresas;

class FacebookEmpresasAdaccounts extends Model
{
    use HasFactory;

    protected $table = 'facebook_empresas_adaccounts';

    protected $fillable = [
        'empresa_id',
        'account_id',
        'account_name',
        'status'
    ];

    public function empresas()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }
}
