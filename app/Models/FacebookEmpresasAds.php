<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FacebookEmpresasAdcampaigns;

class FacebookEmpresasAds extends Model
{
    use HasFactory;

    protected $table = 'facebook_empresas_ads';

    protected $fillable = [
        'adcampaign_id',
        'ad_id',
        'name',
        'insights'
    ];

    protected $casts = [
        'insights' => 'array'
    ];

    public function facebookEmpresasAdcampaigns()
    {
        return $this->belongsTo(FacebookEmpresasAdcampaigns::class, 'adcampaign_id');
    }
}
