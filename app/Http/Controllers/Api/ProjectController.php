<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectOwner;
use App\Models\User;

class ProjectController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(ProjectResource::collection(Project::paginate()), 'RETRIEVE_SUCCESS');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'owner' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if category exists, if not create a new one
        $category = ProjectCategory::firstOrCreate(['name' => $request->category]);

        // check if owner exists, if not create a new one
        $user = User::where('name', $request->owner)->first();
        if (is_null($user)) {
            // overwrite user
            $user = User::create([
                'name' => $request->owner,
                'email' => $request->owner . '@example.com',
                'password' => bcrypt($request->owner),
                'role_id' => 5,
                'isProjectOwner' => 1
            ]);
        } else {
            $user->update([
                'isProjectOwner' => 1
            ]);
        }
        
        // manipulate image storage
        $image_path = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_new_name = time() . $image->getClientOriginalExtension();
            $image->storeAs('uploads/projects', $image_new_name);
        }

        $project = Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $category->id,
            'image' => $image_path ? $image_path : NULL,
        ]);

        // var_dump($user->id, $project->id); die;
        $owner = ProjectOwner::firstOrCreate(
            [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ],
        );

        // var_dump($owner); die;
        
        if (is_null($project)) {
            return $this->sendError('CREATE_FAILED');
        } else {
            return $this->sendResponse(new ProjectResource($project), 'CREATE_SUCCESS');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::find($id);

        if (is_null($project)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            return $this->sendResponse(new ProjectResource($project), 'RETRIEVE_SUCCESS');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'category' => 'required',
            'owner' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if category exists, if not create a new one
        $category = ProjectCategory::firstOrCreate(['name' => $request->category]);

        // check if owner exists, if not create a new one
        $user = User::where('name', $request->owner)->first();
        if (is_null($user)) {
            User::create([
                'name' => $request->owner,
                'email' => $request->owner . '@example.com',
                'password' => bcrypt($request->owner),
            ]);
        }

        // manipulate image storage
        $image_path = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_new_name = time() . $image->getClientOriginalExtension();
            $image->storeAs('uploads/projects', $image_new_name);
        }

        $project = Project::find($id);
        if (is_null($project)) {
            return $this->sendError('UPDATE_FAILED');
        } else {
            $project->title = $request->title;
            $project->description = $request->description;
            $project->category_id = $category->id;
            $project->image = $image_path ? $image_path : NULL;
            $project->save();

            $owner = ProjectOwner::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                ]
            );

            return $this->sendResponse(new ProjectResource($project), 'UPDATE_SUCCESS');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::find($id);

        if (is_null($project)) {
            return $this->sendError('NOT_FOUND');
        }
        
        if ($project->owners->count() > 0) {
            foreach ($project->owners as $owner) {
                $owner->delete();
            }
        }
        
        if ($project->delete()) {
            return $this->sendResponse(new ProjectResource($project), 'DELETE_SUCCESS', 204);
        } else {
            return $this->sendError('DELETE_FAILED');
        }
    }
}
