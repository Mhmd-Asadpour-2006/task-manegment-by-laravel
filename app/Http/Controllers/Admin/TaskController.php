<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use League\Uri\Http;
use Illuminate\Support\Facades\Hash;

class TaskController extends Controller
{
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
