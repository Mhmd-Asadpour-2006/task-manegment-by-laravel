<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use League\Uri\Http;
use Illuminate\Support\Facades\Hash;
class CategoriesController extends Controller
{
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
}
