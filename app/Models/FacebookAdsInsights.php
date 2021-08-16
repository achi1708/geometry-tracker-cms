<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FacebookEmpresasAds;

class FacebookAdsInsights extends Model
{
    use HasFactory;

    protected $table = 'facebook_ads_insights';

    protected $fillable = [
        'ad_id',
        'metric',
        'metric_value'
    ];

    public function ad()
    {
        return $this->belongsTo(FacebookEmpresasAds::class, 'ad_id');
    }
}
