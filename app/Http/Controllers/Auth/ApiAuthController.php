<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ApiAuthController extends Controller
{

    public function get_user(Request $request) {
        $user_auth = $request->user();

        if($user_auth->role){
            switch($user_auth->role){
                case 1:
                    $user_auth->access = array('empresas.access',
                                               'empresas.list',
                                               'empresas.data_social_media');
                    break;
                case 2:
                    $user_auth->access = array('usuarios.access',
                                               'usuarios.list',
                                               'usuarios.create',
                                               'usuarios.edit',
                                               'usuarios.delete',
                                               'empresas.access',
                                               'empresas.list',
                                               'empresas.create',
                                               'empresas.edit',
                                               'empresas.delete',
                                               'empresas.data_social_media');
                    break;
            }
        }

        return $user_auth;
    }

    public function login (Request $request) {

        $rules = [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:8'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if($user)
        {
            if(Hash::check($request->password, $user->password)) 
            {
                $token = $user->createToken(env('AUTH_TOKEN_NAME', 'cff4a88e7d151bcf8589ff650945a65f'))->accessToken;
                return response(['token' => $token], 200);
            } else {
                return response(['errors' => ['Error en las credenciales']], 422);
            }
        } else {
            return response(['errors' => ['Unauthorized']], 422);
        }
    }

    public function logout (Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        return response(['message' => ['Logout Successfully']], 200);
    }

    //provisional method
    public function register_user (Request $request) {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'integer'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $request['role'] = $request['role'] ? intval($request['role']) : 1;

        //dd($request->toArray());

        $user = User::create($request->toArray());
        $token = $user->createToken(env('AUTH_TOKEN_NAME', 'cff4a88e7d151bcf8589ff650945a65f'))->accessToken;
        return response(['token' => $token], 200);
    }
}
