<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Tasks;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules\Can;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tasks = $user->tasks;   

        return response()->json($tasks);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function getAllCategories()
    {
        $allCategories = Categories::query()->get();

        return response()->json($allCategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function add_task(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|min:3',
            'body' => '',
            'priority' => 'required|in:1,2,3',
            'status'=>'required|in:Published,archived',
            'date_of_completion' => 'nullable|date',
            'assignees_emails' => 'nullable|array',
            'category'=>'required|min:3'
        ]);

        $task = Tasks::create($validated);

        $user = $request->user();

        $user->tasks()->attach($task->id);

        $category = Categories::where('user_id', $user->id)->where('title', $validated['category'])->first();
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

    public function remove_task()  {
        
    }

     public function update_task()  {
        
    }

    /**
     * Display the specified resource.
     */
    public function add_category(Request $request)
    {

        $validated = $request->validate([
            'title'=>'required|min:3'
        ]);

        $user = $request->user();

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

    /**
     * Show the form for editing the specified resource.
     */
    public function remove_category(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'title'=>'required|min:3'
        ]);

        $category = Categories::where('user_id', $user->id)
                         ->where('title', $validated['title'])
                         ->first(); 

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted ',
            'data' => $category
        ],201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_category(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'title'=>'required|min:3',
            'new_title'=>'required|min:3'
        ]);

        $category = Categories::where('user_id', $user->id)
                         ->where('title', $validated['title'])
                         ->first(); 

        if (!$category) {
            return response()->json([
                'error' => 'Category not found'
            ], 404);
        }
        $category->update([
            'title'=>$validated['new_title']
        ]);

        return response()->json([
            'message' => 'Category deleted ',
            'data' => $category
        ],201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function not_found()
    {
        return response()->json([
            'error'=>'not found'
        ],404);
    }
}
