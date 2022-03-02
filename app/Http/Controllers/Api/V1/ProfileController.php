<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;

class ProfileController extends Controller
{
    public function show_by_id(Profile $profile)
    {
        return response()->json(new ProfileResource($profile->loadMissing('skills', 'spoken_languages')));
    }

    public function show()
    {
        $profile = auth()->user()->profile;

        if (! $profile) {
            return $this->validationError('error', 'Profile not found.');
        }

        return response()->json(new ProfileResource($profile->loadMissing('skills', 'spoken_languages')));
    }

    public function store(ProfileRequest $request)
    {
        $profile = Profile::updateOrCreate(['user_id' => auth()->id()], $request->validated());

        if ($request->has('username')) {
            auth()->user()->update(['username' => $request->username]);
        }

        if ($request->has('skills')) {
            $profile->skills()->delete();
            foreach ($request->get('skills') as $skill) {
                $profile->skills()->create(['name' => $skill]);
            }
        }

        if ($request->has('spoken_languages')) {
            $profile->spoken_languages()->delete();
            foreach ($request->get('spoken_languages') as $language) {
                $profile->spoken_languages()->create(['name' => $language]);
            }
        }

        return response()->json(new ProfileResource($profile->loadMissing('skills', 'spoken_languages')));
    }

    public function store_step_2(Request $request)
    {
        $request->validate([
            'description' => 'nullable',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id',
            'video_presentation' => 'nullable | file',
            'portfolio' => 'nullable | file',
        ]);

        $profile = auth()->user()->profile;

        $profile->update([
            'description' => $request->description,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
        ]);

        return response()->json(new ProfileResource($profile->loadMissing('skills', 'spoken_languages')));
    }

    public function upload_file(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable | file',
            'video_presentation' => 'nullable | file',
            'portfolio' => 'nullable | file',
        ]);

        if ($request->has('avatar')) {
            auth()->user()->addMedia($request->avatar)->toMediaCollection(User::AVATAR_COLLECTION_NAME);

            return response()->json(['message' => 'success']);
        }

        if (! auth()->user()->profile) {
            return $this->validationError('error', 'Profile not found');
        }

        if ($request->has('video_presentation')) {
            auth()->user()->profile->addMedia($request->video_presentation)->toMediaCollection(Profile::PRESENTATION_COLLECTION_NAME);
        }

        if ($request->has('portfolio')) {
            auth()->user()->profile->addMedia($request->portfolio)->toMediaCollection(Profile::PORTFOLIO_COLLECTION_NAME);
        }

        return response()->json(['message' => 'success']);
    }

    public function update_is_active(Request $request)
    {
        $request->validate([
            'is_active' => 'required | boolean',
        ]);

        $profile = auth()->user()->profile;

        if (! $profile) {
            return $this->validationError('error', 'Profile not found.');
        }

        $profile->update(['is_active' => $request->is_active]);

        return response()->json(new ProfileResource($profile->loadMissing('skills', 'spoken_languages')));
    }

    public function min_max_price()
    {
        return response()->json([
            'min_price' => Profile::min('per_hour'),
            'max_price' => Profile::max('per_hour'),
            'max_review' => Profile::withCount('reviews')->get()->max('reviews_count'),
        ]);
    }

    public function filter(Request $request, Category $category)
    {
        $request->validate([
            'min_price' => 'nullable | numeric',
            'max_price' => 'nullable | numeric',
            'min_rates' => 'nullable | numeric',
            'max_rates' => 'nullable | numeric',
            'min_reviews' => 'nullable | numeric',
            'max_reviews' => 'nullable | numeric',
        ]);

        $profiles = Profile::with('user', 'skills', 'spoken_languages')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->when($request->min_price, function ($query, $min_price) {
                $query->where('per_hour', '>=', $min_price);
            })->when($request->max_price, function ($query, $max_price) {
                $query->where('per_hour', '<=', $max_price);
            })->withAvg('reviews', 'rate')
            ->withCount('reviews')
            ->paginate();

        $collection = $profiles->getCollection();

        if ($min_rates = $request->min_rates) {
            $collection = $collection->where('reviews_avg_rate', '>=', $min_rates);
        }

        if ($max_rates = $request->max_rates) {
            $collection = $collection->where('reviews_avg_rate', '<=', $max_rates);
        }

        if ($min_reviews = $request->min_reviews) {
            $collection = $collection->where('reviews_count', '>=', $min_reviews);
        }

        if ($max_reviews = $request->max_reviews) {
            $collection = $collection->where('reviews_count', '<=', $max_reviews);
        }

        $profiles->setCollection($collection);

        return response()->json(ProfileResource::collection($profiles)->response()->getData(true));
    }

    public function search(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'nullable | string',
        ]);

        $profiles = Profile::with('user', 'skills', 'spoken_languages')
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->when($request->name, function ($query, $name) {
                $query->where('first_name', 'like', '%'.$name.'%');
                $query->orWhere('last_name', 'like', '%'.$name.'%');
            })->paginate();

        return response()->json(ProfileResource::collection($profiles)->response()->getData(true));
    }
}
