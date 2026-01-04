<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Tasks;
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


}
