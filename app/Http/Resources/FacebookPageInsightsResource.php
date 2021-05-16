<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacebookPageInsightsResource extends JsonResource
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
            'empresa' => $this->empresas,
            'metric' => $this->metric,
            'metric_date' => $this->metric_date,
            'metric_value' => $this->metric_value
          ];
    }
}
