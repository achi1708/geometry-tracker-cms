<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresas;
use App\Models\App;
use App\Models\User;
use App\Models\InstragramMedia;
use App\Http\Resources\InstagramMediaResource;

Use App\Http\Traits\FacebookApiTrait;

use Illuminate\Support\Facades\DB;

use FacebookAds\Object\User as FbUser;
use FacebookAds\Object\Page;
use FacebookAds\Object\IGUser as IgUser;
use FacebookAds\Object\IGMedia;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Http\Exception\AuthorizationException;

use App\Exports\InstagramMediaExport;
use Maatwebsite\Excel\Facades\Excel;

use \Exception;

class InstagramController extends Controller
{
    //

    use FacebookApiTrait;

    public function getInstagramMedia(Request $request)
    {
        $rules = [
            'emp' => 'required|exists:empresas,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        
        $instagramMedia = InstragramMedia::where('empresa_id', $request['emp']);

        if(isset($request['orderCol'])){
            $orderCol = '';
            switch($request['orderCol']){
                case "timestamp":
                    $orderCol = 'timestamp';
                    break;
            }

            if($orderCol != ''){
                if(isset($request['orderDir']) && strtolower($request['orderDir']) == 'asc'){
                    $instagramMedia = $instagramMedia->orderBy($orderCol, $request['orderDir']);
                }elseif(isset($request['orderDir'])){
                    $instagramMedia = $instagramMedia->orderBy($orderCol, 'desc');
                }
            }else{
                $instagramMedia = $instagramMedia->orderBy('timestamp', 'desc');
            }
        }else{
            $instagramMedia = $instagramMedia->orderBy('timestamp', 'desc');
        }

        $instagramMedia = $instagramMedia->paginate(50);

        return InstagramMediaResource::collection($instagramMedia);
    }

    public function readIgData(Request $request)
    {
        $rules = [
            'emp' => 'required|exists:empresas,id',
            'process' => 'required|in:media'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $empresa = Empresas::find($request['emp']);
        $process = $request['process'];
        $access_token = false;
        $access_token_time = false;
        $app_secret = env('FB_APP_SECRET');
        $app_id = env('FB_APP_ID');
        $userid = false;
        $account_id = false;
        $page_access_token = false;
        $instagram_account_id = false;

        if($empresa->fb_account_id){
            $account_id = $empresa->fb_account_id;
        }

        if(!$access_token){
            if(isset($request['fat']) && isset($request['ftt'])){
                $access_token = $request['fat'];
                $access_token_time = $request['ftt'];
            }

            if(isset($request['fuid'])){
                $userid = $request['fuid'];
            }
        }

        if(!$access_token){
            return response(['errors' => ['No se cuenta con el acceso correcto al usuario de Facebook que administra esta empresa']], 422);
        }

        if(!$account_id){
            if(!$userid){
                return response(['errors' => ['No se cuenta con el acceso correcto al usuario de Facebook que administra esta empresa']], 422);
            }else{
                $account_id_search = (isset($request['account_id_select']) ? $request['account_id_select'] : false);
                $get_account_fb = $this->get_fb_user_accounts($app_id, $app_secret, $access_token, $userid, $account_id_search);

                if($get_account_fb['status'] != 'Ok'){
                    return response(['errors' => [$get_account_fb['msg']]], 422);
                }else{
                    if($get_account_fb['msg'] == "multiple_accounts"){
                        return response(['msg' => "MULTIPLE_ACCOUNTS", 'msg_extra' => $get_account_fb['data']], 200);
                    }else{
                        $account_id = $get_account_fb['data']['account_id'];
                        $page_access_token = $get_account_fb['data']['page_access_token'];
                        $instagram_account_id = $get_account_fb['data']['instagram_account_id'];
                    }
                }
            }
        }else{
            $account_id_search = $account_id;
            $get_account_fb = $this->get_fb_user_accounts($app_id, $app_secret, $access_token, $userid, $account_id_search);

            if($get_account_fb['status'] != 'Ok'){
                return response(['errors' => [$get_account_fb['msg']]], 422);
            }else{
                if($get_account_fb['msg'] == "multiple_accounts"){
                    return response(['msg' => "MULTIPLE_ACCOUNTS", 'msg_extra' => $get_account_fb['data']], 200);
                }else{
                    $account_id = $get_account_fb['data']['account_id'];
                    $page_access_token = $get_account_fb['data']['page_access_token'];
                    $instagram_account_id = $get_account_fb['data']['instagram_account_id'];
                }
            }
        }

        if(!$instagram_account_id){
            return response(['errors' => ['No se puede obtener la cuenta de instagram asociada a la cuenta de esta empresa']], 422);
        }

        $process_result = false;
        $process_msg = '';
        switch($process){
            case "media":

               //Trae media posts from instagram account
               $info_ig_media = $this->get_ig_media($app_id, $app_secret, $access_token, $instagram_account_id);
               $info_ig_stories = $this->get_ig_stories($app_id, $app_secret, $access_token, $instagram_account_id);

               if($info_ig_stories['status'] == 'Ok' && is_array($info_ig_stories['data'])){
                    $this->save_ig_media_stories($info_ig_stories['data'], $request['emp']);
               }
               
               if($info_ig_media['status'] == 'Ok'){
                    $this->save_ig_media_stories($info_ig_media['data'], $request['emp']);
                    $process_result = true;
                    $process_msg = 'Se han actualizado todos los posts media de la cuenta de instagram de esta empresa';
                }else{
                    $process_result = false;
                    $process_msg = 'No se pudo extraer la info de media de la cuenta de Instagram de esta empresa, intente más adelante';
                }
                break;
        }

        if($process_result){
            $empresa->fb_account_id = $account_id;
            $empresa->save();

            return response(['msg' => "PROCESO_OK", 'msg_extra' => $process_msg], 200);
        }else{
            return response(['errors' => [$process_msg]], 422);
        }
    }

    private function get_ig_media($app_id, $app_secret, $access_token, $ig_account_id){
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        try{
            $fields = array(
                'id',
                'ig_id',
                'caption',
                'comments_count',
                'like_count',
                'media_product_type',
                'media_type',
                'media_url',
                'owner',
                'permalink',
                'timestamp',
                'username',
                'insights.metric(engagement,impressions,reach,saved)'
            );

            $params = array(
                'limit' => 100
            );

            $info_media = (new IgUser($ig_account_id))->getMedia(
                $fields,
                $params
              )->getResponse()->getContent();

            if(is_array($info_media) && isset($info_media['data'])){
                if(count($info_media['data']) > 0){
                    $info_media_final = array();
                    foreach($info_media['data'] as $media_item){
                        if($media_item['media_type'] == 'CAROUSEL_ALBUM' || $media_item['media_type'] == 'VIDEO'){
                            $insights = $this->get_ig_media_other_insights($app_id, $app_secret, $access_token, $media_item['id'], $media_item['media_type']);

                            if($insights){
                                $media_item['insights'] = $insights;
                            }
                        }

                        array_push($info_media_final, $media_item);
                    }

                    $return['status'] = 'Ok';
                    $return['msg'] = 'info_ok';
                    $return['data'] = $info_media_final;
                }else{
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información de media de la cuenta';
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información de media de la cuenta';
            }
        } catch(Exception $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información de media de la cuenta';
        }

        return $return;

    }

    private function get_ig_media_other_insights($app_id, $app_secret, $access_token, $ig_media_id, $ig_media_type){
        $return = false;
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        $metrics = '';

        switch($ig_media_type){
            case "CAROUSEL_ALBUM":
                $metrics = 'engagement,impressions,reach,saved,carousel_album_engagement,carousel_album_impressions,carousel_album_reach,carousel_album_saved,carousel_album_video_views';
                break;
            case "VIDEO":
                $metrics = 'engagement,impressions,reach,saved,video_views';
                break;
        }

        if($metrics == ''){
            return false;
        }

        try{
            $fields = array(
            );

            $params = array(
                'metric' => $metrics
            );

            $info_insights = (new IGMedia($ig_media_id))->getInsights(
                $fields,
                $params
              )->getResponse()->getContent();

            if(is_array($info_insights) && isset($info_insights['data'])){
                if(count($info_insights['data']) > 0){
                    $return = $info_insights;
                }else{
                    $return = false;
                }
            }else{
                $return = false;
            }
        } catch(Exception $e){
            $return = false;
        }

        return $return;

    }

    private function get_ig_stories($app_id, $app_secret, $access_token, $ig_account_id){
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        try{
            $fields = array(
                'id',
                'ig_id',
                'caption',
                'comments_count',
                'like_count',
                'media_product_type',
                'media_type',
                'media_url',
                'owner',
                'permalink',
                'timestamp',
                'username',
                'insights.metric(exits,impressions,reach,replies)'
            );

            $params = array(
                'limit' => 100
            );

            $info_stories = (new IgUser($ig_account_id))->getStories(
                $fields,
                $params
              )->getResponse()->getContent();

            /*if(is_array($info_published_post) && isset($info_published_post['data'])){
                if(count($info_published_post['data']) > 0){
                    $return['status'] = 'Ok';
                    $return['msg'] = 'info_ok';
                    $return['data'] = $info_published_post['data'];
                }else{
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información de publish post de la cuenta';
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información de publish post de la cuenta';
            }*/
        } catch(Exception $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información de media de la cuenta';
        }

        return $return;

    }

    private function save_ig_media_stories($info_media, $empresa_id)
    {
        if(count($info_media) > 0){
            DB::transaction(function () use( $info_media, $empresa_id ) {
                foreach($info_media as $media_item){
                    if(isset($media_item['id']) && isset($media_item['ig_id'])){
                        $data_media = array('media_id' => $media_item['id'],
                                           'ig_id' => '-',
                                           'caption' => '-',
                                           'comments_count' => '-',
                                           'like_count' => '-',
                                           'media_product_type' => '-',
                                           'media_type' => '-',
                                           'media_url' => '-',
                                           'owner' => '[]',
                                           'permalink' => '-',
                                           'timestamp' => '-',
                                           'username' => '-',
                                           'insights' => '[]');
                        $data_media_filled = false;

                        foreach($media_item as $field => $value){
                            if(isset($data_media[$field])){
                                $data_media[$field] = $value;
                                $data_media_filled = true;
                            }
                        }

                        if(!$data_media_filled){
                            continue;
                        }

                        $instagram_media = InstragramMedia::where('empresa_id', $empresa_id)
                                            ->where('media_id', $media_item['id'])
                                            ->first();

                        if($instagram_media){
                            $instagram_media->update($data_media);
                        }else{
                            $data_media['empresa_id'] = $empresa_id;
                            $instagram_media = InstragramMedia::create($data_media);
                        }
                    }
                }
            });
        }
    }

    public function exportInstagramMedia($empresa)
    {
        $instagramMedia = InstragramMedia::where('empresa_id', $empresa)->orderBy('timestamp', 'desc');

        $instagram_media = $instagramMedia->get()->map(function($media){
            $main = ['media_id' => $media->media_id,
                     'ig_id' => $media->ig_id,
                     'caption' => $media->caption,
                     'comments_count'    => $media->comments_count,
                     'like_count'    => $media->like_count,
                     'media_product_type'    => $media->media_product_type,
                     'media_type'    => $media->media_type,
                     'media_url'    => $media->media_url,
                     'permalink'    => $media->permalink,
                     'username'    => $media->username,
                     'creado'  => date("Y-m-d H:i:s", strtotime($media->timestamp)),
                     'engagement' => null,
                     'impressions' => null,
                     'reach' => null,
                     'saved' => null,
                     'video_views' => null,
                     'carousel_album_engagement' => null,
                     'carousel_album_impressions' => null,
                     'carousel_album_reach' => null,
                     'carousel_album_saved' => null,
                     'carousel_album_video_views' => null
                   ];

            if (isset($media->insights['data']) && is_array($media->insights['data']))
            {
                foreach($media->insights['data'] as $k_insight => $insight){
                    if(array_key_exists($insight['name'], $main)){
                        $main[$insight['name']] = $insight['values'][0]['value'];
                    }
                }
            }

            return $main;
        });

        return Excel::download(new InstagramMediaExport($instagram_media), 'instagram_media_'.strtotime(date('Y-m-d H:i:s')).'.xlsx');
    }
}
