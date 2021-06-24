<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstagramMediaResource extends JsonResource
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
            'media_id' => $this->media_id,
            'ig_id' => $this->ig_id,
            'caption' => $this->caption,
            'comments_count' => $this->comments_count,
            'like_count' => $this->like_count,
            'media_product_type' => $this->media_product_type,
            'media_type' => $this->media_type,
            'media_url' => $this->media_url,
            'owner' => $this->owner,
            'permalink' => $this->permalink,
            'username' => $this->username,
            'timestamp' => date("Y-m-d H:i:s", strtotime($this->timestamp)),
            'insights' => $this->getInsights()
          ];
    }

    protected function getInsights()
    {
        $insights = array();

        if(isset($this->insights['data']) && is_array($this->insights['data'])){
            if(count($this->insights['data']) > 0){
                $insights = array_map(function($data){
                    return array('name' => $data['name'], 'description' => $data['description'], 'title' => $data['title'], 'values'=> $data['values']);
                }, $this->insights['data']);
            }
        }

        return $insights;
    }
}
