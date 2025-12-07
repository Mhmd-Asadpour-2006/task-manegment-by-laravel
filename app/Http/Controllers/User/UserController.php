<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use League\Uri\Http;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('role','user')->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
 
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token_type' => 'Bearer',
            'token'   => $token
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        $allowed = ['name', 'email', 'password'];

        if (array_diff(array_keys($request->all()), $allowed)) {
            return response()->json(['error' => 'Extra fields are not allowed'], 400);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $users = User::all();
        foreach ($users as $u) {
            if (Hash::check($validated['password'], $u->password)) {
                return response()->json(['error' => 'Password or email already used by another user'], 400);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return response()->json(['message' => 'User registered!'], 201);
    }


    /**
     * Display the specified resource.
     */
   public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
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

        $admins = json_encode($admins, JSON_PRETTY_PRINT);

        return response()->json($admins);
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

        $users = User::where('role','admin')->get();
        foreach ($users as $u) {
            if ($validated['email'] ===  $u->email){
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

    public function  update_admin(Request $request,$id) {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::where('role','admin')->where('id',$id)->first();

        if(!$user){
            return response()->json(['error'=>'not found'],404);
        }

        $users = User::where('role','admin')->get();
        foreach ($users as $u) {
            if (Hash::check($validated['password'], $u->password)) {
                return response()->json(['error' => 'Password already used by another user'], 400);
            }
        }

        $users = User::where('role','admin')->get();
        foreach ($users as $u) {
            if ($validated['email'] ===  $u->email){
                return response()->json(['error' => 'Password or email already used by another user'], 400);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'admin';
        User::update($validated);

        return response()->json(['message'=>'admin is updated'],201);

    }
}
