<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'created_at' => (string) $this->created_at,
            'role_id' => $this->role,
            'role_desc' => ($this->role == 2) ? "Super Admministrador" : "Usuario Regular Cms",
            'empresas' => $this->empresas,
          ];
    }
}
