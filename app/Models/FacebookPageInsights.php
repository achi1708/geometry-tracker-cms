<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresas;

class FacebookPageInsights extends Model
{
    use HasFactory;

    protected $table = 'facebook_page_insights';

    protected $fillable = [
        'empresa_id',
        'metric',
        'metric_date',
        'metric_value'
    ];

    public function empresas()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }
}
