<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Empresas;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$users = User::with('empresas')->paginate(15);
        $users = User::with('empresas');

        if(isset($request['orderCol'])){
            if(isset($request['orderDir']) && strtolower($request['orderDir']) == 'asc'){
                $users = $users->orderBy($request['orderCol'], $request['orderDir']);
            }elseif(isset($request['orderDir'])){
                $users = $users->orderBy($request['orderCol'], 'desc');
            }
        }

        $users = $users->paginate(15);

        return UserResource::collection($users);
    }

    public function usersFilter(Request $request)
    {
        $users = User::with('empresas');

        if(isset($request['role'])){
            $users = $users->where('role', $request['role']);
        }

        $users = $users->get();

        return UserResource::collection($users);
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
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'integer|in:1,2',
            'empresas' => 'sometimes|array|exists:empresas,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $request['role'] = $request['role'] ? intval($request['role']) : 1;

        $user_arr = $request->except(['empresas']);

        $user = User::create($user_arr);

        //Si envian empresas
        if(isset($request['empresas']) && is_array($request['empresas'])){
            $this->attachEmpresasToUser($request['empresas'], $user);
        }
        
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('empresas')->find($id);

        if(!$user){
            return response(['errors' => ['Access Denied']], 422);
        }

        return new UserResource($user);
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
        $user = User::find($id);

        if(!$user){
            return response(['errors' => ['Access Denied']], 422);
        }

        if ($request->user()->id == $id) {
            return response(['errors' => ['You cannot edit your user']], 422);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
            'empresas' => 'sometimes|array|exists:empresas,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);

        $user_arr = $request->only(['name', 'password']);

        $user->update($user_arr);

        if(isset($request['empresas']) && is_array($request['empresas'])){
            $this->attachEmpresasToUser($request['empresas'], $user);
        }

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);

        if(!$user){
            return response(['errors' => ['Access Denied']], 422);
        }

        if ($request->user()->id == $id) {
            return response(['errors' => ['You cannot delete your user']], 422);
        }

        $user->delete();

        return response(['msg' => 'Done'], 204);
    }

    private function attachEmpresasToUser(Array $empresas, User $user)
    {
        $empresas = collect($empresas);

        $empresas_ids = Empresas::all()->map(function($empresa){
            return $empresa->id;
        });
        $empresas_ids = $empresas_ids->toArray();

        if(count($empresas_ids)){
            $new_empresas_ids = $empresas->filter(function($id) use($empresas_ids){
                return in_array($id, $empresas_ids);
            });
            $new_empresas_ids = $new_empresas_ids->unique()->toArray();

            if(count($new_empresas_ids) > 0){
                $user->empresas()->detach();
                $user->empresas()->attach($new_empresas_ids);
            }
        }
    }
}
