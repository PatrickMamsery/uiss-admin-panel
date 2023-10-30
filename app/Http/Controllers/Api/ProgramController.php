<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Models\ProgramCategory;

/**
 * @group Program management
 *
 * APIs for managing programs
 */
class ProgramController extends BaseController
{
    /**
     * Get all programs
     *
     * This endpoint retrieves all programs paginated in chunks of 15.
     *
     * @queryParam page The page number to retrieve. Example: 1
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(ProgramResource::collection(Program::paginate()), 'RETRIEVE_SUCCESS');
    }

    /**
     * Create a new program
     *
     * This endpoint creates a new program.
     *
     * @bodyParam name string required The name of the program. Example: Program 1
     * @bodyParam description string required The description of the program. Example: This is a program
     * @bodyParam category string required The category of the program. Example: Category 1
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'category' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if category exists, if not create a new one
        $category = ProgramCategory::firstOrCreate(['name' => $request->category]);

        $program = Program::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $category->id,
        ]);

        if (is_null($program)) {
            return $this->sendError('CREATE_FAILED');
        } else {
            return $this->sendResponse(new ProgramResource($program), 'CREATE_SUCCESS');
        }
    }

    /**
     * Get a program
     *
     * This endpoint retrieves a program by its ID.
     *
     * @urlParam id required The ID of the program. Example: 1
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $program = Program::find($id);

        if (is_null($program)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            return $this->sendResponse(new ProgramResource($program), 'RETRIEVE_SUCCESS');
        }
    }

    /**
     * Update a program
     *
     * This endpoint updates a program.
     *
     * @urlParam id required The ID of the program. Example: 1
     *
     * @bodyParam name string The name of the program. Example: Program 1
     * @bodyParam description string The description of the program. Example: This is a program
     * @bodyParam category string The category of the program. Example: Category 1
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'description' => 'nullable',
            'category' => 'nullable'
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if category exists, if not create a new one
        $category = ProgramCategory::firstOrCreate(['name' => $request->category]);

        $program = Program::find($id);

        if (is_null($program)) {
            return $this->sendError('UPDATE_FAILED');
        } else {
            $program->name = $request->name;
            $program->description = $request->description;
            $program->category_id = $category->id;
            $program->save();

            return $this->sendResponse(new ProgramResource($program), 'UPDATE_SUCCESS');
        }
    }

    /**
     * Delete a program
     *
     * This endpoint deletes a program and all it's relations completely.
     *
     * <aside class="notice"> <strong>NOTE:</strong> This action is irreversible </aside>
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $program = Program::find($id);

        if (is_null($program)) {
            return $this->sendError('NOT_FOUND');
        }

        if ($program->delete()) {
            return $this->sendResponse(new ProgramResource($program), 'DELETE_SUCCESS', 204);
        } else {
            return $this->sendError('DELETE_FAILED');
        }
    }
}
