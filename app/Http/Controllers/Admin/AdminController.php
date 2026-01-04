<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Tasks;
use App\Models\User;
use League\Uri\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function admin_login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('role','admin')->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
 
        $token = $user->createToken('auth_admin_token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token_type' => 'Bearer',
            'token'   => $token
        ]);
    }

     /**
     * Update the specified resource in storage.
     */
    // public function admin_register(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name'     => 'required|string|max:255',
    //         'email'    => 'required|email|unique:users,email',
    //         'password' => 'required|min:8',
    //     ]);

    //     $validated['password'] = Hash::make($validated['password']);
    //     User::create($validated);
    // }

    /**
     * Remove the specified resource from storage.
     */

    

    public function get_all_admin(Request $request)
    {
        // if (!$request->user()->tokenCan('admin')) {
        //     abort(403);
        // }  for staus not define gate in  app/providers/AppServiceProvider funtion boot
        $user = $request->user();

        $admins = User::where('role','admin')->get();

        return response()->json([
            'admins'=>$admins
        ]);
    }

    public function get_all_user_admin(Request $request){
        $users = User::where('role','user')->get();

    
        return response()->json([
            'users'=> $users
        ]);
    }


    public function add_user_admin(Request $request) {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);
        
        $users = User::where('role','user')->get();
        foreach ($users as $u) {
            if (Hash::check($validated['password'], $u->password)) {
                return response()->json(['error' => 'Password or email already used by another user'], 400);
            }
        }


        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'user';
        User::create($validated);
        
        return response()->json(['message'=>'User is added'],201);
    }


    public function remove_user_admin(Request $request)  {
        $validated = $request->validate([
            'id'     => 'required',
        ]);

        $user = User::find($validated['id']);
        if(!$user){
            return response()->json(['error'=>'user not found'],404);
        }
        $user->delete();

        return response()->json(['message'=>'user is removed'],201);
    }

       public function update_user_admin(Request $request)  {
        $validated = $request->validate([
            'id' => 'required',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('role','user')->where('id',$validated['id'])->first();

        if(!$user){
            return response()->json(['error'=>'not found'],404);
        }

        $users = User::where('role','user')->get();
        foreach ($users as $u) {
            if($user != $u ){
                if (Hash::check($validated['password'], $u->password)) {
                    return response()->json(['error' => 'Password already used by another user'], 400);
                }
                if ($validated['email'] ===  $u->email){
                    return response()->json(['error' => 'Password or email already used by another user'], 400);
                }
            }
        }

        
        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'user';
        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role' => $validated['role'],
            'password'=>$validated['password']
        ]);

        return response()->json(['message'=>'user is updated'],201);
    }

    public function add_admin(Request $request){

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $users = User::where('role','admin')->get();
        foreach ($users as $u) {
            if (Hash::check($validated['password'], $u->password)) {
                return response()->json(['error' => 'Password or email already used by another user'], 400);
            }
        }


        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'admin';
        User::create($validated);
        
        return response()->json(['message'=>'admin is added'],201);
    }


     public function remove_admin(Request $request){
        $validated = $request->validate([
            'id'     => 'required',
        ]);
        $admin = User::find($validated['id']);
        if(!$admin){
            return response()->json(['error'=>'user not found'],404);
        }
        $admin->delete();

        return response()->json(['message'=>'admin is removed'],201);
    }

    public function  update_admin(Request $request) {
        $validated = $request->validate([
            'id'=>'required',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('role','admin')->where('id',$validated['id'])->first();

        if(!$user){
            return response()->json(['error'=>'not found'],404);
        }

        $users = User::where('role','admin')->get();
        foreach ($users as $u) {
            if($user != $u ){
                if (Hash::check($validated['password'], $u->password)) {
                    return response()->json(['error' => 'Password already used by another user'], 400);
                }
                if ($validated['email'] ===  $u->email){
                    return response()->json(['error' => 'Password or email already used by another user'], 400);
                }
            }
        }

        
        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'admin';
        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role' => $validated['role'],
            'password'=>$validated['password']
        ]);

        return response()->json(['message'=>'admin is updated'],201);

    }
}
