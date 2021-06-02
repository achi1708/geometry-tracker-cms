<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacebookPublishedPostsResource extends JsonResource
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
            'post_id' => $this->post_id,
            'message' => $this->message,
            'picture' => $this->picture,
            'reach'   => $this->getInsightValue('post_impressions_unique'),
            'engagement' => $this->getInsightValue('post_engaged_users'),
            'likes'   => $this->getInsightValue('post_reactions_like_total'),
            'is_expired' => $this->is_expired,
            'expired' => ($this->is_expired == 1) ? 'Si' : 'No',
            'is_hidden' => $this->is_hidden,
            'hidden' => ($this->is_hidden == 1) ? 'Si' : 'No',
            'is_popular' => $this->is_popular,
            'popular' => ($this->is_popular == 1) ? 'Si' : 'No',
            'is_published' => $this->is_published,
            'published' => ($this->is_published == 1) ? 'Si' : 'No',
            'tags' => $this->getMessageTags(),
            'insights' => $this->getInsights(),
            'created_time' => date("Y-m-d H:i:s", strtotime($this->created_time))
          ];
    }

    protected function getMessageTags() 
    {
        $tags = array();

        if(is_array($this->message_tags)){
            $tags = array_map(function($data){
                return $data['name'];
            }, $this->message_tags);
        }

        return $tags;
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

    protected function getInsightValue($name)
    {
        $valor = '-';

        if(isset($this->insights['data']) && is_array($this->insights['data'])){
            if(count($this->insights['data']) > 0){
                foreach($this->insights['data'] as $insight){
                    if($insight['name'] == $name){
                        $valor = $insight['values'][0]['value'];
                        break;
                    }
                }
            }
        }

        return $valor;
    }
}
