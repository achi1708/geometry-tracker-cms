<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacebookAdCampaignsResource extends JsonResource
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
            'adaccount' => $this->facebookEmpresasAdaccounts,
            'campaign_id' => $this->campaign_id,
            'campaign_name' => $this->campaign_name,
            'objective' => $this->objective,
            'status' => $this->status,
            'budget_remaining' => $this->budget_remaining,
            'buying_type' => $this->buying_type,
            'configured_status' => $this->configured_status,
            'daily_budget' => $this->daily_budget,
            'effective_status' => $this->effective_status,
            'issues_info' => $this->issues_info,
            'created_time' => date("Y-m-d H:i:s", strtotime($this->created_time)),
            'start_time' => date("Y-m-d H:i:s", strtotime($this->start_time)),
            'stop_time' => date("Y-m-d H:i:s", strtotime($this->stop_time))
          ];
    }
}
