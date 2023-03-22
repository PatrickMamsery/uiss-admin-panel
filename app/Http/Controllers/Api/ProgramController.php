<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Models\ProgramCategory;

class ProgramController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(ProgramResource::collection(Program::paginate()), 'RETRIEVE_SUCCESS');
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
