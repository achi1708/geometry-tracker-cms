<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacebookAdAdsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'adcampaign' => $this->facebookEmpresasAdcampaigns,
            'adaccount' => $this->facebookEmpresasAdcampaigns->facebookEmpresasAdaccounts,
            'ad_id' => $this->ad_id,
            'name' => $this->name,
            'insights' => $this->getInsights()
          ];
    }

    protected function getInsights()
    {
        $insights = array();

        if($this->insights != '[]' && is_array($this->insights)){
            if(count($this->insights) > 0){
                foreach($this->insights as $k => $insights_nodes){
                    foreach($insights_nodes as $item => $value){
                        if(is_array($value) && $item == 'actions'){
                            foreach($value as $j => $sub_nodes){
                                $insights[] = array('metric' => $sub_nodes['action_type'], 'value' => $sub_nodes['value']);
                            }
                        }else{
                            $insights[] = array('metric' => $item, 'value' => $value);
                        }
                    }
                }
            }
        }

        return $insights;
    }
}
