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
            'insights' => $this->insights
          ];
    }
}
