<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
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
            'name' => $this->name,
            'logo' => $this->logo,
            'created_at' => (string) $this->created_at,
            'users' => $this->users,
            'apps' => $this->apps,
            'f_a_t' => $this->fb_access_token,
            'f_t_t' => $this->fb_token_time,
            'f_a_i' => $this->fb_account_id,
            'f_active' => $this->verifyToken()
          ];
    }

    protected function verifyToken() 
    {
        $flag = false;

        if(strlen($this->fb_access_token) > 0 && strlen($this->fb_token_time)){
            if(intval($this->fb_token_time) > strtotime(date('Y-m-d'))){
                $flag = true;
            }
        }

        return $flag;
    }
}
