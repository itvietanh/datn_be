<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function index()
    {
        $fillable = ['uuid','role_name','description','created_at','updated_at','created_by','updated_by'];
        $data = Role::select($fillable)->paginate($req->input('size:10'));
        return $this->getPaging($data);
    }

    public function store(Request $request)
    {
        $validated = $req->validate([
            'role_name'=>'required|string',
            'description'=>'required|string',
        ]);

        $params = $req->all();
        $validated = $req->all();
        $role = Role::create([
            'uuid'=> \Illumante\Support\Str::uuid(),
            'role_name' => $validated['role_name'],
            'description' => $validated['description']
        ]);
        return  $this->responseSuccess($role,201);
    }


    public function show(string $id)
    {
        $role = Role::where('uuid',$uuid)->firstOrFail();
        return $this->oneResponse($role);
    }


    public function update(Request $request, string $id)
    {
        $validated = $req->validate([
            'role_name'=> 'required|string',
            'description'=>'required|string',
        ]);
        $role = Role::where('uuid',$uuid)->firstOrFail();
        $role->update([
            'role_name'=>$validated['role_name'],
            'description'=>$validated['desctiption'],
            'updated_at'=>now(),
        ]);
        return $this->responseSuccess($role);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::where('uuid',$uuid)->firstOrFail();
        $role-> delete();
        return $this->responseSuccess([
            'message'=>'Role deleted successfully'
        ]);
    }
}
