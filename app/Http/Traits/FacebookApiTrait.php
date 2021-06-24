<?php

namespace App\Http\Traits;

use FacebookAds\Object\User as FbUser;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Http\Exception\AuthorizationException;

use \Exception;

trait FacebookApiTrait {

    public function get_fb_user_accounts($app_id, $app_secret, $access_token, $userid, $acount_id_search = false)
    {
        $return = array('status' => 'Ok', 'msg' => '', 'data' => '');
        $api = Api::init($app_id, $app_secret, $access_token);
        $api->setLogger(new CurlLogger());

        $fields = array(
            'access_token',
            'category',
            'category_list',
            'name',
            'id',
            'tasks',
            'instagram_business_account'
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
                    $instagram_account_id = false;
                    foreach($info_accounts['data'] as $account_data){
                        $listado_accounts[] = array('id' => $account_data['id'],
                                                    'name' => $account_data['name'],
                                                    'instagram_id' => (isset($account_data['instagram_business_account']) ? $account_data['instagram_business_account']['id'] : false));

                        if($acount_id_search){
                            if($account_data['id'] == $acount_id_search){
                                $account_id = $account_data['id'];
                                $page_access_token = $account_data['access_token'];

                                if(isset($account_data['instagram_business_account'])){
                                    if(isset($account_data['instagram_business_account']['id'])){
                                        $instagram_account_id = $account_data['instagram_business_account']['id'];
                                    }
                                }
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
                                                'page_access_token' => $page_access_token,
                                                'instagram_account_id' => $instagram_account_id);
                    }
                }elseif(count($info_accounts['data']) < 1){
                    $return['status'] = 'Error';
                    $return['msg'] = 'No se obtuvo la información de cuentas businesses asociadas a este usuario de Facebook [002]';
                }else{
                    if(isset($info_accounts['data'][0]['id'])){
                        $return['status'] = 'Ok';
                        $return['msg'] = 'info_ok';
                        $return['data'] = array('account_id' => $info_accounts['data'][0]['id'],
                                                'page_access_token' => $info_accounts['data'][0]['access_token'],
                                                'instagram_account_id' => false);

                        if(isset($info_accounts[0]['instagram_business_account'])){
                            if(isset($info_accounts[0]['instagram_business_account']['id'])){
                                $return['data']['instagram_account_id'] = $info_accounts[0]['instagram_business_account']['id'];
                            }
                        }
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

}