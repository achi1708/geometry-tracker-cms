<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresas;
use App\Models\App;
use App\Models\User;
use App\Models\FacebookPublishPosts;
use App\Models\FacebookPageInsights;
use App\Models\FacebookEmpresasAdaccounts;
use App\Models\FacebookEmpresasAdcampaigns;
use App\Models\FacebookEmpresasAds;
use App\Http\Resources\EmpresaResource;
use Illuminate\Support\Facades\DB;

use FacebookAds\Object\User as FbUser;
use FacebookAds\Object\Page;
use FacebookAds\Object\Business;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Http\Exception\AuthorizationException;

use \Exception;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $superAdmin = FALSE;
        $id_user = $request->user()->id;
        if($request->user()->role >= 2){
            $superAdmin = TRUE;
        }

        if($superAdmin){
            $empresas = Empresas::all();
        }else{
            $empresas = Empresas::whereHas('users', function(Builder $q) use($id_user) {
                $q->where('users.id', '=', $id_user);
            })->get();
        }

        return EmpresaResource::collection($empresas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'logo' => 'sometimes|image',
            'apps' => 'required',
            'users' => 'sometimes'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $empresa_logo = false;
        if(isset($request['logo'])){
            $path = $request->file('logo')->getRealPath();
            $logo = file_get_contents($path);
            $base64 = base64_encode($logo);
            $empresa_logo = $base64;
        }

        $empresa_arr = $request->except(['apps', 'users', 'logo']);
        if($empresa_logo){
            $empresa_arr['logo'] = $empresa_logo;
        }

        $empresa = Empresas::create($empresa_arr);

        //Si envian apps
        if(is_string($request['apps'])){
            $request['apps'] = explode(",", $request['apps']);
        }
        if(isset($request['apps']) && is_array($request['apps'])){
            $this->attachAppsToEmpresa($request['apps'], $empresa);
        }

        //Si envian users
        if(is_string($request['users'])){
            $request['users'] = explode(",", $request['users']);
        }
        if(isset($request['users']) && is_array($request['users'])){
            $this->attachUsersToEmpresa($request['users'], $empresa);
        }
        
        return new EmpresaResource($empresa);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $empresa = Empresas::find($id);

        if(!$empresa){
            return response(['errors' => ['Access Denied']], 422);
        }

        return new EmpresaResource($empresa);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $empresa = Empresas::find($id);

        if(!$empresa){
            return response(['errors' => ['Access Denied']], 422);
        }

        $rules = [
            'name' => 'required|string|max:255',
            //'logo' => 'sometimes|image',
            'apps' => 'sometimes',
            'users' => 'sometimes'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $empresa_logo = false;
        if(isset($request['logo']) && $request['logo'] != null && $request['logo'] != ''){
            $path = $request->file('logo')->getRealPath();
            $logo = file_get_contents($path);
            $base64 = base64_encode($logo);
            $empresa_logo = $base64;
        }

        $empresa_arr = $request->except(['apps', 'users', 'logo']);
        if($empresa_logo){
            $empresa_arr['logo'] = $empresa_logo;
        }

        $empresa->update($empresa_arr);

        //Si envian apps
        if(is_string($request['apps'])){
            $request['apps'] = explode(",", $request['apps']);
        }
        if(isset($request['apps']) && is_array($request['apps'])){
            $this->attachAppsToEmpresa($request['apps'], $empresa);
        }

        //Si envian users
        if(is_string($request['users'])){
            $request['users'] = explode(",", $request['users']);
        }
        if(isset($request['users']) && is_array($request['users'])){
            $this->attachUsersToEmpresa($request['users'], $empresa);
        }
        
        return new EmpresaResource($empresa);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Tener en cuenta que si tiene data de FB o Google descargada no se elimina
    }

    public function readFbData(Request $request)
    {
        $rules = [
            'emp' => 'required|exists:empresas,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $empresa = Empresas::find($request['emp']);
        $access_token = false;
        $app_secret = env('FB_APP_SECRET');
        $app_id = env('FB_APP_ID');
        $userid = false;
        $account_id = false;
        $page_access_token = false;


        if($empresa->fb_access_token != '' && $empresa->fb_token_time != ''){
            if(intval($empresa->fb_token_time) > strtotime(date('Y-m-d'))){
                $access_token = $empresa->fb_access_token;

                if($empresa->fb_account_id){
                    $account_id = $empresa->fb_account_id;
                }

                if($empresa->fb_user_logged_id){
                    $userid = $empresa->fb_user_logged_id;
                }
            }
        }

        if(!$access_token){
            if(isset($request['fat']) && isset($request['ftt'])){
                $access_token = $request['fat'];
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
                    }
                }
            }
        }

        //Trae datos de publish post 
        /*$info_published_post = $this->get_fb_publish_post($app_id, $app_secret, $page_access_token, $account_id);

        if($info_published_post['status'] == 'Ok'){
            $this->save_publish_post($info_published_post['data'], $request['emp']);
        }

        //Trae los datos de insights de la page
        $info_page_insights = $this->get_fb_page_insights($app_id, $app_secret, $page_access_token, $account_id);

        if($info_page_insights['status'] == 'Ok'){
            $this->save_page_insights($info_page_insights['data'], $request['emp']);
        }*/

        //
        $business_ad_id_search = (isset($request['business_ad_id_select']) ? $request['business_ad_id_select'] : false);
        $get_fb_business_adaccounts = $this->get_fb_business_adaccounts($app_id, $app_secret, $access_token, $userid, $business_ad_id_search);

        if($get_fb_business_adaccounts['status'] == 'Ok'){
            $this->save_empresas_fb_adaccounts($get_fb_business_adaccounts['data'], $request['emp']);

            $campaigns_by_adaccount = array();
            foreach($get_fb_business_adaccounts['data'] as $adaccount_info){
                if($adaccount_info['account_status'] == 1){
                    $campaigns_by_adaccount[$adaccount_info['id']] = array();
                    $get_campaigns = $this->get_fb_adaccounts_campaigns($app_id, $app_secret, $access_token, $adaccount_info['id']);

                    if($get_campaigns['status'] == 'Ok'){
                        $campaigns_by_adaccount[$adaccount_info['id']] = $get_campaigns['data'];
                    }
                }
            }

            if(count($campaigns_by_adaccount) > 0){
                $this->save_empresas_fb_adcampaigns($campaigns_by_adaccount, $request['emp']);

                foreach($campaigns_by_adaccount as $adaccount_id => $campaigns){
                    foreach($campaigns as $key => $campaign){
                        $get_ads = $this->get_fb_adcampaign_ads($app_id, $app_secret, $access_token, $campaign['id']);

                        if($get_ads['status'] == 'Ok'){
                            $campaigns_by_adaccount[$adaccount_id][$key]['ads_list'] = $get_ads['data'];
                        }
                    }
                }

                foreach($campaigns_by_adaccount as $adaccount_id => $campaigns){
                    foreach($campaigns as $key => $campaign){
                        if(isset($campaign['ads_list'])){
                            $this->save_empresas_fb_ads($campaign['ads_list'], $campaign['id'], $adaccount_id, $request['emp']);
                        }
                    }
                }
            }
        }

        dd("fin 6");

        $data_actual = array($access_token,$app_secret,$app_id,$userid,$account_id);
        

        return response(['msg' => $data_actual], 200);
    }

    private function attachAppsToEmpresa(Array $apps, Empresas $empresa)
    {
        $apps = collect($apps);

        $apps_ids = App::all()->map(function($app){
            return $app->id;
        });
        $apps_ids = $apps_ids->toArray();
        
        if(count($apps_ids)){
            $new_apps_ids = $apps->filter(function($id) use($apps_ids){
                return in_array($id, $apps_ids);
            });
            $new_apps_ids = $new_apps_ids->unique()->toArray();

            if(count($new_apps_ids) > 0){
                $empresa->apps()->detach();
                $empresa->apps()->attach($new_apps_ids);
            }
        }
    }

    private function attachUsersToEmpresa(Array $users, Empresas $empresa)
    {
        $users = collect($users);

        $users_ids = User::all()->map(function($user){
            return $user->id;
        });
        $users_ids = $users_ids->toArray();
        
        if(count($users_ids)){
            $new_users_ids = $users->filter(function($id) use($users_ids){
                return in_array($id, $users_ids);
            });
            $new_users_ids = $new_users_ids->unique()->toArray();

            if(count($new_users_ids) > 0){
                $empresa->users()->detach();
                $empresa->users()->attach($new_users_ids);
            }
        }
    }

    private function get_fb_user_accounts($app_id, $app_secret, $access_token, $userid, $acount_id_search = false)
    {
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        $fields = array(
        );

        $params = array(
        );

        try{
            $info_accounts = (new FbUser($userid))->getAccounts(
                $fields,
                $params
              )->getResponse()->getContent();

            if(is_array($info_accounts) && $info_accounts['data']){
                if(count($info_accounts['data']) > 1){
                    $listado_accounts = array();
                    $account_id = false;
                    $page_access_token = false;
                    foreach($info_accounts['data'] as $account_data){
                        $listado_accounts[] = array('id' => $account_data['id'],
                                                    'name' => $account_data['name']);

                        if($acount_id_search){
                            if($account_data['id'] == $acount_id_search){
                                $account_id = $account_data['id'];
                                $page_access_token = $account_data['access_token'];
                                break;
                            }
                        }
                    }

                    if(!$account_id){
                        $return['status'] = 'Ok';
                        $return['msg'] = 'multiple_accounts';
                        $return['data'] = $listado_accounts;
                    }else{
                        $return['status'] = 'Ok';
                        $return['msg'] = 'info_ok';
                        $return['data'] = array('account_id' => $account_id,
                                                'page_access_token' => $page_access_token);
                    }
                }elseif(count($info_accounts['data']) < 1){
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se obtuvo la información de cuentas businesses asociadas a este usuario de Facebook [002]';
                }else{
                    if(isset($info_accounts['data'][0]['id'])){
                        $return['status'] = 'Ok';
                        $return['msg'] = 'info_ok';
                        $return['data'] = array('account_id' => $info_accounts['data'][0]['id'],
                                                'page_access_token' => $info_accounts['data'][0]['access_token']);
                    }else{
                        $return['status'] = 'Error';
                        $return['msg'] = 'No se obtuvo la información de cuentas businesses asociadas a este usuario de Facebook [003]';
                    }
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se obtuvo la información de cuentas businesses asociadas a este usuario de Facebook [001]';
            }
        } catch(AuthorizationException $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se obtuvo la información de cuentas businesses asociadas a este usuario de Facebook';
        } catch(Exception $e) {
            $return['status'] = 'Error';
            $return['msg'] = $e->getMessage();
        }

        return $return;
    }

    private function get_fb_publish_post($app_id, $app_secret, $page_access_token, $account_id){
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $page_access_token);
        $api->setLogger(new CurlLogger());

        try{
            $fields = array(
                'created_time',
                'message',
                'id',
                'application',
                'is_expired',
                'is_hidden',
                'is_popular',
                'is_published',
                'message_tags',
                'picture',
                'properties',
                'insights.metric(post_engaged_users,page_posts_impressions,page_posts_impressions_organic,page_posts_impressions_viral,post_engaged_fan,post_clicks,post_impressions,post_impressions_fan){id,description,description_from_api_doc,name,period,title,values}'
            );

            $params = array(
                'limit' => 100
            );

            $info_published_post = (new Page($account_id))->getPublishedPosts(
                $fields,
                $params
              )->getResponse()->getContent();

            if(is_array($info_published_post) && isset($info_published_post['data'])){
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
            }
        } catch(Exception $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información de publish post de la cuenta';
        }

        return $return;

    }

    private function get_fb_page_insights($app_id, $app_secret, $page_access_token, $account_id)
    {
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $page_access_token);
        $api->setLogger(new CurlLogger());

        try{
            $fields = array(
            );

            $params = array(
                'metric' => 'page_engaged_users,page_post_engagements,page_consumptions,page_consumptions_unique,page_negative_feedback,page_negative_feedback_unique,page_impressions,page_impressions_unique,page_impressions_paid,page_impressions_paid_unique,page_impressions_viral,page_impressions_viral_unique,page_video_views,page_video_views_unique,page_video_views_paid,page_video_views_organic,page_views_total,page_views_logout,page_views_logged_in_total,page_views_logged_in_unique',
                'date_preset' => 'last_30d',
                'period' => 'day'
            );

            $info_page_insights = (new Page($account_id))->getInsights(
                $fields,
                $params
            )->getResponse()->getContent();

            if(is_array($info_page_insights) && isset($info_page_insights['data'])){
                if(count($info_page_insights['data']) > 0){
                    $return['status'] = 'Ok';
                    $return['msg'] = 'info_ok';
                    $return['data'] = $info_page_insights['data'];
                }else{
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información de insights de la cuenta';
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información de insights de la cuenta';
            }
        } catch(Exception $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información de insights de la cuenta';
        }

        return $return;
    }

    private function get_fb_business_adaccounts($app_id, $app_secret, $access_token, $userid, $business_ad_id_search = false)
    {
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        $fields = array(
        );

        $params = array(
        );

        $business_id_final = false;

        try{
            $user_businesses = (new FbUser($userid))->getBusinesses(
                $fields,
                $params
              )->getResponse()->getContent();

            if(is_array($user_businesses) && isset($user_businesses['data'])){
                if(count($user_businesses['data']) > 1){
                    $listado_businesses = array();
                    foreach($user_businesses['data'] as $user_business){
                        $listado_businesses[] = array('id' => $user_business['id'],
                                                    'name' => $user_business['name']);

                        if($business_ad_id_search){
                            if($user_business['id'] == $business_ad_id_search){
                                $business_id_final = $user_business['id'];
                                break;
                            }
                        }
                    }

                    if(!$business_id_final){
                        $return['status'] = 'Ok';
                        $return['msg'] = 'multiple_businesses_ids';
                        $return['data'] = $listado_businesses;
                    }
                }elseif(count($user_businesses['data']) < 1){
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa [002]';
                }else{
                    if(isset($user_businesses['data'][0]['id'])){
                        $business_id_final = $user_businesses['data'][0]['id'];
                    }else{
                        $return['status'] = 'Error';
                        $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa [003]';
                    }
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa [001]';
            }
        } catch(Exception $e) {
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa';
        }

        if($business_id_final){
            try{
                $fields = array(
                    'id',
                    'account_id',
                    'name',
                    'account_status'
                );
        
                $params = array(
                );

                $business_ad_accounts = (new Business($business_id_final))->getOwnedAdAccounts(
                    $fields,
                    $params
                )->getResponse()->getContent();
                
                if(is_array($business_ad_accounts) && isset($business_ad_accounts['data'])){
                    if(count($business_ad_accounts['data']) > 0){
                        $business_ad_accounts_list = array_filter($business_ad_accounts['data'], function($data){
                            return ($data['account_status'] == 1);
                        });

                        $return['status'] = 'Ok';
                        $return['msg'] = 'info_ok';
                        $return['data'] = $business_ad_accounts_list;
                    }else{
                        $return['status'] = 'Error';
                        $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa [005]';
                    }
                }else{
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa [006]';
                }

            } catch(Exception $e) {
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información para poder obtener Ads de la empresa [004]';
            }
            
        }

        return $return;
    }

    private function get_fb_adaccounts_campaigns($app_id, $app_secret, $access_token, $adaccount_id)
    {
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        try{

            $fields = array(
                'id',
                'name',
                'objective',
                'status',
                'budget_remaining',
                'buying_type',
                'configured_status',
                'daily_budget',
                'effective_status',
                'issues_info',
                'created_time',
                'start_time',
                'stop_time'
            );

            $params = array(
            );

            $info_adaccount_campaigns = (new AdAccount($adaccount_id))->getCampaigns(
                $fields,
                $params
            )->getResponse()->getContent();

            if(is_array($info_adaccount_campaigns) && isset($info_adaccount_campaigns['data'])){
                if(count($info_adaccount_campaigns['data']) > 0){
                    $return['status'] = 'Ok';
                    $return['msg'] = 'info_ok';
                    $return['data'] = $info_adaccount_campaigns['data'];
                }else{
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información de campañas para esta adaccount';
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información de campañas para esta adaccount';
            }
        
        } catch(Exception $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información de campañas para esta adaccount';
        }

        return $return;
    }

    private function get_fb_adcampaign_ads($app_id, $app_secret, $access_token, $adcampaign_id)
    {
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        try{

            $fields = array(
                'id',
                'name',
                'insights'
            );

            $params = array(
            );

            $info_adcampaign_ads = (new Campaign($adcampaign_id))->getAds(
                $fields,
                $params
            )->getResponse()->getContent();

            if(is_array($info_adcampaign_ads) && isset($info_adcampaign_ads['data'])){
                if(count($info_adcampaign_ads['data']) > 0){
                    $return['status'] = 'Ok';
                    $return['msg'] = 'info_ok';
                    $return['data'] = $info_adcampaign_ads['data'];
                }else{
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se encuentra información de ads para esta campaña';
                }
            }else{
                $return['status'] = 'Error';
                $return['msg'] = 'No se encuentra información de ads para esta campaña';
            }
        
        } catch(Exception $e){
            $return['status'] = 'Error';
            $return['msg'] = 'No se encuentra información de ads para esta campaña';
        }

        return $return;
    }

    private function save_publish_post($info_publish_posts, $empresa_id)
    {
        if(count($info_publish_posts) > 0){
            DB::transaction(function () use( $info_publish_posts, $empresa_id ) {
                foreach($info_publish_posts as $published_post){
                    if(isset($published_post['id'])){
                        $data_post = array('post_id' => $published_post['id'],
                                           'message' => '-',
                                           'application' => '[]',
                                           'is_expired' => '-',
                                           'is_hidden' => '-',
                                           'is_popular' => '-',
                                           'is_published' => '-',
                                           'message_tags' => '[]',
                                           'picture' => '-',
                                           'properties' => '[]',
                                           'insights' => '[]',
                                           'created_time' => '-');
                        $data_post_filled = false;

                        foreach($published_post as $field => $value){
                            if(isset($data_post[$field])){
                                $data_post[$field] = $value;
                                $data_post_filled = true;
                            }
                        }

                        if(!$data_post_filled){
                            continue;
                        }

                        $facebook_post = FacebookPublishPosts::where('empresa_id', $empresa_id)
                                            ->where('post_id', $published_post['id'])
                                            ->first();

                        if($facebook_post){
                            $facebook_post->update($data_post);
                        }else{
                            $data_post['empresa_id'] = $empresa_id;
                            $facebook_post = FacebookPublishPosts::create($data_post);
                        }
                    }
                }
            });
        }
    }

    private function save_page_insights($info_page_insights, $empresa_id)
    {
        if(count($info_page_insights) > 0){
            DB::transaction(function () use( $info_page_insights, $empresa_id ) {
                foreach($info_page_insights as $insights_data){
                    if($insights_data['period'] == 'day' && isset($insights_data['name']) && isset($insights_data['values'])){
                        if(count($insights_data['values']) > 0){
                            foreach($insights_data['values'] as $value_per_day){
                                if(isset($value_per_day['end_time']) && isset($value_per_day['value'])){
                                    $data_insight = array('empresa_id' => $empresa_id,
                                                          'metric'     => $insights_data['name'],
                                                          'metric_date'=> date("Y-m-d", strtotime($value_per_day['end_time'])),
                                                          'metric_value' => $value_per_day['value']
                                                        );

                                    $facebook_page_insight = FacebookPageInsights::where('empresa_id', $empresa_id)
                                                                        ->where('metric', $data_insight['metric'])
                                                                        ->where('metric_date', $data_insight['metric_date'])
                                                                        ->first();
            
                                    if($facebook_page_insight){
                                        $facebook_page_insight->update($data_insight);
                                    }else{
                                        $facebook_page_insight = FacebookPageInsights::create($data_insight);
                                    }
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    private function save_empresas_fb_adaccounts($info_emp_adaccounts, $empresa_id)
    {
        if(count($info_emp_adaccounts) > 0){
            $adaccounts_ids = array_map(function($data){
                return $data['id'];
            }, $info_emp_adaccounts);

            DB::transaction(function () use( $info_emp_adaccounts, $empresa_id, $adaccounts_ids ) {

                $empresa_adaccounts = FacebookEmpresasAdaccounts::where('status', 1)
                                                                ->where('empresa_id', $empresa_id)
                                                                ->get();

                foreach($empresa_adaccounts as $empresa_adaccount){
                    if(!in_array($empresa_adaccount->account_id, $adaccounts_ids)){
                        $empresa_adaccount->status = 0;
                        $empresa_adaccount->save();
                    }
                }

                foreach($info_emp_adaccounts as $info_emp_adaccount){
                    $existe_adaccount = FacebookEmpresasAdaccounts::where('account_id', $info_emp_adaccount['id'])
                                                                ->where('empresa_id', $empresa_id)
                                                                ->first();

                    if($existe_adaccount){
                        $existe_adaccount->account_name = $info_emp_adaccount['name'];
                        $existe_adaccount->status = ($info_emp_adaccount['account_status'] == 1 ? $info_emp_adaccount['account_status'] : 0);
                        $existe_adaccount->save();
                    }else{
                        $data_new_adaccount = array('empresa_id' => $empresa_id,
                                                    'account_id' => $info_emp_adaccount['id'],
                                                    'account_name' => $info_emp_adaccount['name'],
                                                    'status' => ($info_emp_adaccount['account_status'] == 1 ? $info_emp_adaccount['account_status'] : 0));

                        $new_emp_adaccount = FacebookEmpresasAdaccounts::create($data_new_adaccount);                            
                    }
                }
            });
        }
    }

    private function save_empresas_fb_adcampaigns($campaigns_by_adaccount, $empresa_id)
    {
        if(count($campaigns_by_adaccount) > 0){
            DB::transaction(function () use( $campaigns_by_adaccount, $empresa_id ) {
                foreach($campaigns_by_adaccount as $adaccount_id => $campaigns_items){
                    $get_adaccount = FacebookEmpresasAdaccounts::where('account_id', $adaccount_id)
                                                                ->where('empresa_id', $empresa_id)
                                                                ->first();

                    if($get_adaccount){
                        foreach($campaigns_items as $campaigns_item){
                            if(isset($campaigns_item['id'])){
                                $data_campaign = array('adaccount_id' => $get_adaccount->id,
                                                   'campaign_id' => $campaigns_item['id'],
                                                   'campaign_name' => '',
                                                   'objective' => '',
                                                   'status' => '',
                                                   'budget_remaining' => '',
                                                   'buying_type' => '',
                                                   'configured_status' => '',
                                                   'daily_budget' => '',
                                                   'effective_status' => '',
                                                   'issues_info' => '',
                                                   'created_time' => '',
                                                   'start_time' => '',
                                                   'stop_time' => '');

                                $data_campaign_filled = false;
        
                                foreach($campaigns_item as $field => $value){
                                    if($field == 'name'){
                                        $data_campaign['campaign_name'] = $value;
                                        $data_campaign_filled = true;
                                    }elseif(isset($data_campaign[$field])){
                                        $data_campaign[$field] = $value;
                                        $data_campaign_filled = true;
                                    }
                                }
        
                                if(!$data_campaign_filled){
                                    continue;
                                }
        
                                $campaign_exists = FacebookEmpresasAdcampaigns::where('adaccount_id', $get_adaccount->id)
                                                    ->where('campaign_id', $campaigns_item['id'])
                                                    ->first();
        
                                if($campaign_exists){
                                    $campaign_exists->update($data_campaign);
                                }else{
                                    $campaign_new = FacebookEmpresasAdcampaigns::create($data_campaign);
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    private function save_empresas_fb_ads($ads_list, $campaign_id, $adaccount_id, $empresa_id){
        if(count($ads_list) > 0){
            DB::transaction(function () use( $ads_list, $campaign_id, $adaccount_id, $empresa_id ) {
                $get_emp_account = FacebookEmpresasAdaccounts::where('empresa_id', $empresa_id)
                                                            ->where('account_id', $adaccount_id)
                                                            ->first();

                if($get_emp_account){
                    $get_campaign = FacebookEmpresasAdcampaigns::where('adaccount_id', $get_emp_account->id)
                                                            ->where('campaign_id', $campaign_id)
                                                            ->first();

                    if($get_campaign){
                        foreach($ads_list as $ad_item){
                            $ad_exists = FacebookEmpresasAds::where('adcampaign_id', $get_campaign->id)
                                                            ->where('ad_id', $ad_item['id'])
                                                            ->first();

                            if($ad_exists){
                                $ad_exists->name = $ad_item['name'];
                                $ad_exists->insights = (isset($ad_item['insights']) ? $ad_item['insights']['data'] : '[]' );
                            }else{
                                $new_ad_data = array('adcampaign_id' => $get_campaign->id,
                                                     'ad_id'         => $ad_item['id'],
                                                     'name'          => $ad_item['name'],
                                                     'insights'      => (isset($ad_item['insights']) ? $ad_item['insights']['data'] : '[]' )
                                                    );

                                $ad_new = FacebookEmpresasAds::create($new_ad_data);
                            }
                        }
                    }                                                            
                }
            });
        }
    }
}
