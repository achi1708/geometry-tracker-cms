<?php

namespace App\Exports;

use App\Models\FacebookEmpresasAdaccounts;
use App\Models\FacebookEmpresasAdcampaigns;
use App\Models\FacebookEmpresasAds;
use App\Http\Resources\FacebookAdAdsResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacebookAdsPerSheet implements FromCollection, WithTitle, WithHeadings
{
    private $empresa_id;
    private $account_id;
    private $account_info;
    private $sheet_titles;
    private $ads_insights_metrics;

    public function __construct($empresa, $account_id)
    {
        $this->empresa_id = $empresa;
        $this->account_id  = $account_id;

        $this->account_info = FacebookEmpresasAdaccounts::find($account_id);

        $this->sheet_titles = array('AD_Id',
                                    'AD_name',
                                    'Campaign_id',
                                    'Campaign_name',
                                    'Campaign_objective',
                                    'Campaign_status',
                                    'Campaign_budget_remaining',
                                    'Campaign_buying_type',
                                    'Campaign_config_status',
                                    'Campaign_daily_budget',
                                    'Campaign_effective_status',
                                    'Campaign_created_time',
                                    'Campaign_start_time',
                                    'Campaign_stop_time');

        $this->ads_insights_metrics = $this->get_ads_unique_metrics();
        if(count($this->ads_insights_metrics) > 0){
            foreach($this->ads_insights_metrics as $insight_metric){
                $this->sheet_titles[] = $insight_metric;
            }
        }

    }

    public function collection()
    {
        $records = array();
        $rset_ini = FacebookEmpresasAds::with('facebookEmpresasAdcampaigns')->join('facebook_empresas_adcampaigns', 'facebook_empresas_ads.adcampaign_id', '=', 'facebook_empresas_adcampaigns.id')
                                        ->join('facebook_empresas_adaccounts', 'facebook_empresas_adcampaigns.adaccount_id', '=', 'facebook_empresas_adaccounts.id')
                                        ->where('facebook_empresas_adaccounts.empresa_id', $this->empresa_id)
                                        ->where('facebook_empresas_adaccounts.id', $this->account_id)
                                        ->orderBy('facebook_empresas_adcampaigns.created_time', 'asc')
                                        ->get();

        foreach($rset_ini as $ad_info){
            $array_record = array('AD_Id' => $ad_info['ad_id'],
                                'AD_name' => $ad_info['name'],
                                'Campaign_id' => $ad_info['facebookEmpresasAdcampaigns']['campaign_id'],
                                'Campaign_name' => $ad_info['facebookEmpresasAdcampaigns']['campaign_name'],
                                'Campaign_objective' => $ad_info['facebookEmpresasAdcampaigns']['objective'],
                                'Campaign_status' => $ad_info['facebookEmpresasAdcampaigns']['status'],
                                'Campaign_budget_remaining' => $ad_info['facebookEmpresasAdcampaigns']['budget_remaining'],
                                'Campaign_buying_type' => $ad_info['facebookEmpresasAdcampaigns']['buying_type'],
                                'Campaign_config_status' => $ad_info['facebookEmpresasAdcampaigns']['configured_status'],
                                'Campaign_daily_budget' => $ad_info['facebookEmpresasAdcampaigns']['daily_budget'],
                                'Campaign_effective_status' => $ad_info['facebookEmpresasAdcampaigns']['effective_status'],
                                'Campaign_created_time' => $ad_info['facebookEmpresasAdcampaigns']['created_time'],
                                'Campaign_start_time' => $ad_info['facebookEmpresasAdcampaigns']['start_time'],
                                'Campaign_stop_time' => $ad_info['facebookEmpresasAdcampaigns']['stop_time']);


            $insights_record = $this->get_ad_insights_sheet($ad_info['insights']);

            $array_record = array_merge($array_record, $insights_record);
            $records[] = $array_record;
        }
        
        return collect($records);
    }

    public function title(): string
    {
        return $this->account_info->account_name;
    }

    public function headings(): array
    {
        return $this->sheet_titles;
    }

    protected function get_ads_unique_metrics()
    {
        $return = array();
        
        $facebookAds_init = FacebookEmpresasAds::select('facebook_empresas_ads.insights')->join('facebook_empresas_adcampaigns', 'facebook_empresas_ads.adcampaign_id', '=', 'facebook_empresas_adcampaigns.id')
                                               ->join('facebook_empresas_adaccounts', 'facebook_empresas_adcampaigns.adaccount_id', '=', 'facebook_empresas_adaccounts.id')
                                               ->where('facebook_empresas_adaccounts.empresa_id', $this->empresa_id)
                                               ->where('facebook_empresas_ads.insights', '!=', '[]')
                                               ->orderBy('facebook_empresas_ads.insights', 'desc')
                                               ->get();

        foreach($facebookAds_init as $ad_info){
            foreach($ad_info['insights'] as $key => $insights){
                foreach($insights as $item => $value){
                    if(is_array($value) && $item == 'actions'){
                        foreach($value as $j => $sub_nodes){
                            if(!in_array($sub_nodes['action_type'], $return)){
                                $return[] = $sub_nodes['action_type'];
                            }
                        }
                    }else{
                        if(!in_array($item, $return)){
                            $return[] = $item;
                        }
                    }
                }
            }
        }

        return $return;
    }

    protected function get_ad_insights_sheet($insights)
    {
        $return = array();
        $empty = TRUE;

        if($insights != '[]' && is_array($insights)){
            if(count($insights) > 0){
                $empty = FALSE;

                foreach($this->ads_insights_metrics as $insight_metric){
                    foreach($insights as $k => $insights_nodes){
                        foreach($insights_nodes as $item => $value){
                            if($item == $insight_metric){
                                $return[$insight_metric] = $value;
                                continue 3;
                            }elseif(is_array($value) && $item == 'actions'){
                                foreach($value as $j => $sub_nodes){
                                    if($sub_nodes['action_type'] == $insight_metric){
                                        $return[$insight_metric] = $sub_nodes['value'];
                                        continue 4;
                                    }
                                }
                            }
                        }
                    }

                    if(!isset($return[$insight_metric])){
                        $return[$insight_metric] = "";
                    }
                }
            }
        }
        
        if($empty){
            foreach($this->ads_insights_metrics as $insight_metric){
                $return[$insight_metric] = "";
            }
        }

        return $return;
    }
}
