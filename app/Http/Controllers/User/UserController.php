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

        return response()->json([
            'admins'=>$admins
        ]);
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

    public function get_categories_admin()  {
        $categories = Categories::all();

        return response()->json($categories);
    }

    public function add_Category_admin(Request $request)  {
         $validated = $request->validate([
            'title'=>'required|min:3',
            'user_id'=>'required'
        ]);

        $user = User::where('id',$validated['user_id'])->where('role','user')->first();

        if(!$user){
            return response()->json([
                'error'=>'not found'
            ],404);
        }

        $data = Categories::create([
            'user_id' => $user->id,
            'title' => $validated['title']
        ]);

        if(empty($data)){
            return response()->json(['error' => 'Category not created '],500);
        }
        
        return response()->json([
            'message' => 'Category created ',
            'data' => $data
        ],201);
    }

    public function remove_category_admin(Request $request)  {
        $validated = $request->validate([
            'id'=> 'required'
        ]);

        $category = Categories::where('id',$validated['id'])->first();

        if(!$category){
            return response()->json([
                'error'=>'category not found'
            ],404);
        }

        $category->delete();

        return response()->json([
            'message'=>'category is deleted',
            'data'=>$category
        ]);
    }

    public function update_category_admin(Request $request)  {
        $validated = $request->validate([
            'id'=>'required',
            'title'=>'required|min:3',
            'user_id'=>'required'
        ]);
        
        $category = Categories::where('id',$validated['id'])->first();

        if(!$category){
            return response()->json([
                'error'=>'category not found'
            ],404);
        }

        $user = User::where('id',$validated['user_id'])->where('role','user')->first();

        if(!$user){
            return response()->json([
                'error'=>'user not found'
            ],404);
        }

        $category->update([
            'user_id' => $user->id,
            'title' => $validated['title']
        ]);

        if(empty($category)){
            return response()->json(['error' => 'Category not created '],500);
        }
        
        return response()->json([
            'message' => 'Category created ',
            'data' => $category
        ],201);
    }

    public function get_tasks_admin()  {
        $tasks = Tasks::all();

        return response()->json($tasks);
    }

    public function add_task_admin(Request $request)  {
        $validated = $request->validate([
            'user_id' => 'required',
            'title'    => 'required|min:3',
            'body' => '',
            'priority' => 'required|in:1,2,3',
            'status'=>'required|in:Published,archived',
            'date_of_completion' => 'nullable|date|after_or_equal:today',
            'assignees_emails' => 'nullable|array',
            'category'=>'required'
        ]);

        $user = User::where('id',$validated['user_id'])->where('role','user')->first();

        if(!$user){
            return response()->json([
                'error'=>'user not found'
            ],404);
        }

        $task = Tasks::create([
            'title'    => $validated['title'],
            'body' => $validated['body'],
            'priority' => $validated['priority'],
            'status'=>$validated['status'],
            'date_of_completion' => $validated['date_of_completion'],
            'assignees_emails' => $validated['assignees_emails'],
            'category'=>$validated['category']
        ]);


        $user->tasks()->attach($task->id);

        $category = Categories::where('user_id', $user->id)->where('id', $validated['category'])->first();
        if(!$category){
            $category->tasks()->attach($task->id);
        }
    
        if (!empty($validated['assignees_emails'])) {
            $assignees = User::whereIn('email', $validated['assignees_emails'])->get();
            $task->users()->attach($assignees->pluck('id'));   
        }


        return response()->json([
            'message' => 'Task created and assigned to user.',
            'task' => $task
        ]);
    }

    public function remove_task_admin(Request $request)  {
        $validated = $request->validate([
            'task_id'=>'required',
        ]);

        $task = Tasks::where('id',$validated['task_id'])->first();

        if(!$task){
            return response()->json(['error'=>'task not found'],404);
        }

        $task->users()->detach();
        $task->delete();
    }

    public function update_task_admin(Request $request)  {
        
        $validated = $request->validate([
            'user_id'=>'required',
            'task_id'=>'required',
            'title'    => 'required|min:3',
            'body' => 'nullable',
            'priority' => 'required|in:1,2,3',
            'status'=>'required|in:Published,archived',
            'date_of_completion' => 'nullable|date|after_or_equal:today',
            'assignees_emails' => 'nullable|array',
            'category'=>'required|exists:categories,id'
        ]);

        $task = Tasks::find($validated['task_id']);

        $user = User::where('id',$validated['user_id'])->first();

        if(!$user){
            return response()->json([
                'error'=>'user not found'
            ],404);
        }

        $task->users()->syncWithoutDetaching([$user->id]);


        if(!$task){
            return response()->json(['error'=>'task not found'],404);
        }

        $category = Categories::where('user_id', $user->id)
                          ->where('id', $validated['category'])
                          ->first();

        if(!$category){                  
            return response()->json(['error'=>'category not found'],404);
        }

        $task->update([
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'date_of_completion' => $validated['date_of_completion'] ?? null,
            'category' => $validated['category'] 
        ]);
        
        $mainUserId = intval($validated['user_id']);
        $assignees = [];
        if (!empty($validated['assignees_emails'])) {
            $assignees = User::whereIn('email', $validated['assignees_emails'])->pluck('id')->toArray();
        }

        $syncList = array_unique(array_merge([$mainUserId], $assignees));

        $task->users()->sync($syncList);

        return response()->json([
            'message' => 'Task updated successfully.',
            'task' => $task
        ]);
    }

}
