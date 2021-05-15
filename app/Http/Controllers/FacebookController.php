<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Empresas;
use App\Models\FacebookPublishPosts;
use App\Http\Resources\FacebookPublishedPostsResource;

class FacebookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPublishedPosts(Request $request)
    {
        $rules = [
            'emp' => 'required|exists:empresas,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        
        $facebookPublishedPosts = FacebookPublishPosts::where('empresa_id', $request['emp']);

        if(isset($request['orderCol'])){
            if(isset($request['orderDir']) && strtolower($request['orderDir']) == 'asc'){
                $facebookPublishedPosts = $facebookPublishedPosts->orderBy($request['orderCol'], $request['orderDir']);
            }elseif(isset($request['orderDir'])){
                $facebookPublishedPosts = $facebookPublishedPosts->orderBy($request['orderCol'], 'desc');
            }
        }

        $facebookPublishedPosts = $facebookPublishedPosts->paginate(50);

        return FacebookPublishedPostsResource::collection($facebookPublishedPosts);
    }
}
