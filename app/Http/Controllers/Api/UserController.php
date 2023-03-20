<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\CustomRole;
use App\Models\MemberDetail;
use App\Models\LeaderDetail;
use App\Models\University;
use App\Models\College;
use App\Models\Department;
use App\Models\DegreeProgramme;
use App\Models\Position;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(UserResource::collection(User::paginate()), 'RETRIEVE_SUCCESS');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // var_dump($request->all()); die;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|unique:users',
            'role' => 'nullable',
            'additionalInfo' => 'nullable',
        ]);

        
        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        if ($request->role) {
            // check if the role exists
            // clean the role input to small letters
            $inputRole = strtolower($request->role);

            $role = CustomRole::where('name', $inputRole)->first();
            
            if (is_null($role)) {
                $role = new CustomRole;
                $role->name = $inputRole;
                $role->save();
            }
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $role->id,
            'password' => $request->phone ? bcrypt($request->phone) : bcrypt($request->email),
        ]);

        // var_dump($request->additionalInfo['position']); die;
        
        switch ($request->role) {
            case('member'):
                $role = CustomRole::where('name', 'member')->first();

                // var_dump($request->additionalInfo['regNo']); die;
                
                // check if there are additional info
                if (!$request->additionalInfo) {
                    return $this->sendError('MISSING_ADDITIONAL_INFO');
                } else {
                    // check if the member already exists
                    $member = MemberDetail::where('user_id', $user->id)->first();
                    if (!is_null($member)) {
                        return $this->sendError('DUPLICATE_ENTRY');
                    } else {
                        $member = new MemberDetail;
                        $member->user_id = $user->id;
                        $member->reg_no = $request->additionalInfo['regNo'];
                        $member->area_of_interest = $request->additionalInfo['areaOfInterest'];
                        $member->university_id = University::where('name', $request->additionalInfo['university'])->first()->id;
                        $member->department_id = Department::where('name', $request->additionalInfo['department'])->first()->id;
                        $member->college_id = College::where('name', $request->additionalInfo['college'])->first()->id;
                        $member->degree_programme_id = DegreeProgramme::where('name', $request->additionalInfo['degreeProgramme'])->first()->id;
                        $member->save();
                    }
                }
            break;

            case('leader'):
                $role = CustomRole::where('name', 'leader')->first();

                
                // check if there are additional info
                if (!$request->additionalInfo) {
                    return $this->sendError('MISSING_ADDITIONAL_INFO');
                } else {
                    // check if the member already exists
                    $leader = LeaderDetail::where('user_id', $user->id)->first();
                    if (!is_null($leader)) {
                        return $this->sendError('DUPLICATE_ENTRY');
                    } else {
                        // check if position is available in db or else create new
                        $position = Position::where('title', $request->additionalInfo['position'])->first();
                        // var_dump($position); die;

                        if (is_null($position)) {
                            $position = new Position;
                            $position->title = $request->additionalInfo['position'];

                            $position->save();
                        }

                        $leader = new LeaderDetail;
                        $leader->user_id = $user->id;
                        $leader->position_id = $position->id;
                        $leader->start_date = array_key_exists('startDate', $request->additionalInfo) ? $request->additionalInfo['startDate'] : \Carbon\Carbon::now();
                        $leader->end_date = array_key_exists('endDate', $request->additionalInfo) ? $request->additionalInfo['endDate'] : \Carbon\Carbon::now()->addYear();

                        $leader->save();
                    }
                }

            break;

            // default:
            //     $msg = 'Something went wrong.';
            //     return $this->sendError($msg);
        }

        if (is_null($user)) {
            return $this->sendError('CREATE_FAILED');
        } else {
            return $this->sendResponse(new UserResource($user), 'CREATE_SUCCESS');
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
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('NOT_FOUND');
        }

        return $this->sendResponse(new UserResource($user), 'RETRIEVE_SUCCESS');
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
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('NOT_FOUND');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|unique:users,phone,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($user->save()) {
            return $this->sendResponse(new UserResource($user), 'UPDATE_SUCCESS');
        } else {
            return $this->sendError('UPDATE_FAILED');
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
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('NOT_FOUND');
        }

        if ($user->delete()) {
            return $this->sendResponse(new UserResource($user), 'DELETE_SUCCESS', 204);
        } else {
            return $this->sendError('DELETE_FAILED');
        }
    }
}
