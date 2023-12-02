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

/**
 * @group Project management
 *
 * APIs for managing projects
 */
class ProjectController extends BaseController
{
    /**
     * Get all projects
     *
     * @unauthenticated
     *
     * This endpoint retrieves all projects paginated in chunks of 15.
     *
     * @queryParam page The page number to retrieve. Example: 1
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(ProjectResource::collection(Project::paginate()), 'RETRIEVE_SUCCESS');
    }

    /**
     * Create a new project
     *
     * This endpoint creates a new project.
     *
     * @bodyParam title string required The title of the project. Example: Project 1
     * @bodyParam description string required The description of the project. Example: This is a project
     * @bodyParam category string required The category of the project. Example: Category 1
     * @bodyParam owner string required The owner of the project. Example: John Doe
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

        try {
            // check if category exists, if not create a new one
            $category = ProjectCategory::firstOrCreate(['name' => $request->category]);

            // check if owner exists, if not create a new one
            $user = User::where('name', $request->owner)->first();
            if (is_null($user)) {
                // overwrite user
                $user = User::create([
                    'name' => $request->owner,
                    'email' => strtolower(preg_replace('/\s+/', '', $request->owner)) . '@example.com',
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
            // $image_path = '';
            // if ($request->hasFile('image')) {
            //     $image = $request->file('image');
            //     $image_new_name = time() . $image->getClientOriginalExtension();
            //     $image->storeAs('uploads/projects', $image_new_name);
            // }

            $project = Project::create([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $category->id,
                // 'image' => $image_path ? $image_path : $event->image, // made way for cloudinary image management from frontend
                'image' => $request->image ? $request->image : '',
            ]);

            // var_dump($user->id, $project->id); die;
            $owner = ProjectOwner::firstOrCreate(
                [
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                ],
            );
        } catch (\Throwable $th) {
            return $this->sendError('UPDATE_FAILED', $th->getMessage());
        }


        // var_dump($owner); die;

        if (is_null($project)) {
            return $this->sendError('CREATE_FAILED');
        } else {
            return $this->sendResponse(new ProjectResource($project), 'CREATE_SUCCESS');
        }
    }

    /**
     * Get a single project
     *
     * @unauthenticated
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
     * Update a project
     *
     * This endpoint updates a project.
     *
     * @urlParam id required The ID of the project. Example: 1
     * @bodyParam title string The title of the project. Example: Project 1
     * @bodyParam description string The description of the project. Example: This is a project
     * @bodyParam category string The category of the project. Example: Category 1
     * @bodyParam owner string The owner of the project. Example: John Doe
     * @bodyParam image string The image of the project. Example: image.png
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable',
            'description' => 'nullable',
            'category' => 'nullable',
            'owner' => 'nullable',
            'image' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if category exists, if not create a new one
        $category = ProjectCategory::firstOrCreate(['name' => $request->category]);

        // check if owner exists, if not create a new one
        $user = User::where('name', $request->owner)->first();
        if (is_null($user)) {
            $user = User::create([
                'name' => $request->owner,
                'email' => strtolower(preg_replace('/\s+/', '', $request->owner)) . '@example.com',
                'isProjectOwner' => 1,
                'password' => bcrypt($request->owner),
            ]);
        } else {
            $user->update([
                'isProjectOwner' => 1
            ]);
        }

        // manipulate image storage
        // $image_path = '';
        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $image_new_name = time() . $image->getClientOriginalExtension();
        //     $image->storeAs('uploads/projects', $image_new_name);
        // }

        $project = Project::find($id);
        if (is_null($project)) {
            return $this->sendError('UPDATE_FAILED');
        } else {
            $project->title = $request->title;
            $project->description = $request->description;
            $project->category_id = $category->id;
            $project->image = $request->image ? $request->image : $project->image;
            $project->save();

            // delete all owners
            if ($request->has('owner') && $project->owners->count() > 0) {
                foreach ($project->owners as $owner) {
                    $owner->delete();
                }
            }

            // create new owner
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
     * Delete a project
     *
     * This endpoint deletes a project.
     *
     * <aside class="notice"> <strong>NOTE:</strong> This action is irreversible </aside>
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
