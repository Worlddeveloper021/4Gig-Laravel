<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Http\Resources\Api\V1\CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ($request->with_children)
            ? Category::with('children')->root()->get()
            : Category::root()->get();

        return response()->json(CategoryResource::collection($categories));
    }

    public function profiles_index(Request $request, Category $category)
    {
        $profiles = $category->profiles()->with('skills', 'spoken_languages')->get();

        return response()->json(ProfileResource::collection($profiles));
    }
}
