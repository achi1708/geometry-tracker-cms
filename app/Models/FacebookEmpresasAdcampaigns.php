<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FacebookEmpresasAdaccounts;

class FacebookEmpresasAdcampaigns extends Model
{
    use HasFactory;

    protected $table = 'facebook_empresas_adcampaigns';

    protected $fillable = [
        'adaccount_id',
        'campaign_id',
        'campaign_name',
        'objective',
        'status',
        'budget_remaining',
        'buying_type',
        'configured_status',
        'daily_budget',
        'effective_status',
        'issues_info',
        'created_time',
        'start_time',
        'stop_time'
    ];

    public function facebookEmpresasAdaccounts()
    {
        return $this->belongsTo(FacebookEmpresasAdaccounts::class, 'adaccount_id');
    }
}
