<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\College;
use App\Models\Position;

use App\Models\CustomRole;
use App\Models\Department;
use App\Models\University;
use App\Models\LeaderDetail;
use App\Models\MemberDetail;
use Illuminate\Http\Request;
use App\Models\DegreeProgramme;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\UserResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\LeaderResource;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 100;
        return $this->sendResponse(UserResource::collection(User::paginate($per_page)), 'RETRIEVE_SUCCESS');
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
            'image' => 'nullable',
            'role' => 'nullable',
            'additionalInfo' => 'nullable',
        ]);

        // var_dump($request->all()); die;

        
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

        // var_dump($request->image); die;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $role->id,
            'password' => $request->phone ? bcrypt($request->phone) : bcrypt($request->email),
            'image' => $request->image,
        ]);

        // var_dump($request->additionalInfo['position']); die;
        
        switch ($request->role) {
            case('member'):
                $role = CustomRole::where('name', 'member')->first();
                
                // check if there are additional info
                if ($role && !$request->additionalInfo) {
                    return $this->sendError('MISSING_ADDITIONAL_INFO');
                } else {
                    // check if the member already exists
                    $member = MemberDetail::where('user_id', $user->id)->first();
                    if (!is_null($member)) {
                        return $this->sendError('DUPLICATE_ENTRY');
                    } else {
                        // implement a try catch block to handle errors and create new instances of university, college, department and degree programme

                        try {
                            $data = $this->createMemberDetails($request->additionalInfo['university'], $request->additionalInfo['college'], $request->additionalInfo['department'], $request->additionalInfo['degreeProgramme']);

                            $university = $data['university'];
                            $college = $data['college'];
                            $department = $data['department'];
                            $degreeProgramme = $data['degreeProgramme'];

                        } catch (\Exception $error) {
                            return $this->sendError('CREATE_FAILED', $error);
                        }

                        try {
                            // to do db transaction here
                            DB::transaction(function () use ($user, $request, $member, $university, $college, $department, $degreeProgramme) {
                                
                                $member = new MemberDetail;
                                $member->user_id = $user->id;
                                $member->reg_no = $request->additionalInfo['regNo'];
                                $member->area_of_interest = $request->additionalInfo['areaOfInterest'];
                                $member->university_id = $university->id;
                                $member->department_id = $department->id;
                                $member->college_id = $college->id;
                                $member->degree_programme_id = $degreeProgramme->id;
                                $member->save();
                            });
                        } catch (\Exception $error) {
                            return $this->sendError('CREATE_FAILED', $error);
                        }
                    }
                }
            break;

            case('leader'):
                $role = CustomRole::where('name', 'leader')->first();

                
                // check if there are additional info
                if ($role && !$request->additionalInfo) {
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
        $user = User::with('customRole')->find($id);

        if (is_null($user)) {
            return $this->sendError('NOT_FOUND');
        }

        
        // display different user details based on the role
        switch ($user->customRole->name) {
            case('member'):
                $user = User::with('memberDetails')->find($id);

                return $this->sendResponse(new MemberResource($user), 'RETRIEVE_SUCCESS');
            break;

            case('leader'):
                $user = User::with('leaderDetails')->find($id);

                return $this->sendResponse(new LeaderResource($user), 'RETRIEVE_SUCCESS');
            break;
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
            'name' => 'nullable',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|unique:users,phone,' . $user->id,
            'image' => 'nullable',
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

        // var_dump($inputRole); die;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role_id = $role->id;
        $user->image = $request->image ? $request->image : $user->image;

        switch ($inputRole) {
            case ('member'):
                $role = CustomRole::where('name', 'member')->first();
                
                // check if there are additional info
                if ($role && !$request->additionalInfo) {
                    return $this->sendError('MISSING_ADDITIONAL_INFO');
                } else {
                    // check if the member already exists
                    $member = MemberDetail::where('user_id', $user->id)->first();
                    if (!is_null($member)) {
                        $data = $this->createMemberDetails($request->additionalInfo['university'], $request->additionalInfo['college'], $request->additionalInfo['department'], $request->additionalInfo['degreeProgramme']);

                        $university = $data['university'];
                        $college = $data['college'];
                        $department = $data['department'];
                        $degreeProgramme = $data['degreeProgramme'];

                        try {
                            // to do db transaction here
                            DB::transaction(function () use ($user, $request, $member, $university, $college, $department, $degreeProgramme) {
                                
                                $member->reg_no = $request->additionalInfo['regNo'];
                                $member->area_of_interest = $request->additionalInfo['areaOfInterest'];
                                $member->university_id = $university->id;
                                $member->college_id = $college->id;
                                $member->department_id = $department->id;
                                $member->degree_programme_id = $degreeProgramme->id;
                                $member->save();
                            });
                        } catch (\Exception $error) {
                            return $this->sendError('UPDATE_FAILED', $error);
                        }
                    } else {
                        // implement a try catch block to handle errors and create new instances of university, college, department and degree programme

                        try {
                            $data = $this->createMemberDetails($request->additionalInfo['university'], $request->additionalInfo['college'], $request->additionalInfo['department'], $request->additionalInfo['degreeProgramme']);

                            $university = $data['university'];
                            $college = $data['college'];
                            $department = $data['department'];
                            $degreeProgramme = $data['degreeProgramme'];

                        } catch (\Exception $error) {
                            return $this->sendError('UPDATE_FAILED', $error);
                        }

                        try {
                            // to do db transaction here
                            DB::transaction(function () use ($user, $request, $member, $university, $college, $department, $degreeProgramme) {
                                
                                $member = new MemberDetail;
                                $member->user_id = $user->id;
                                $member->reg_no = $request->additionalInfo['regNo'];
                                $member->area_of_interest = $request->additionalInfo['areaOfInterest'];
                                $member->university_id = $university->id;
                                $member->department_id = $department->id;
                                $member->college_id = $college->id;
                                $member->degree_programme_id = $degreeProgramme->id;
                                $member->save();
                            });
                        } catch (\Exception $error) {
                            return $this->sendError('UPDATE_FAILED', $error);
                        }
                    }
                }
            break;

            case ('leader'):
                $role = CustomRole::where('name', 'leader')->first();

                
                // check if there are additional info
                if ($role && !$request->additionalInfo) {
                    return $this->sendError('MISSING_ADDITIONAL_INFO');
                } else {
                    // check if the member already exists
                    $leader = LeaderDetail::where('user_id', $user->id)->first();
                    if (!is_null($leader)) {
                        
                        // check if position attribute exists in payload
                        if (array_key_exists('position', $request->additionalInfo)) {
                            $position = Position::where('title', $request->additionalInfo['position'])->first();
                            
                            if (is_null($position)) {
                                $position = new Position;
                                $position->title = $request->additionalInfo['position'];
                                
                                $position->save();
                            }
                        }
                        
                        
                        $leader->user_id = $user->id;
                        $leader->position_id = $position ? $position->id : $leader->position_id;
                        $leader->start_date = array_key_exists('startDate', $request->additionalInfo) ? $request->additionalInfo['startDate'] : $leader->start_date;
                        $leader->end_date = array_key_exists('endDate', $request->additionalInfo) ? $request->additionalInfo['endDate'] : $leader->end_date;
                        $leader->save(); // update leader details
                        // var_dump($leader->id); die;
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
            
            default:
                # code...
                break;
        }

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

        // var_dump(count(MemberDetail::where('user_id', $user->id)->get())); die;
        try {
            // check if user has relations then delete them
            if ($user->customRole->name == 'member') {
                $memberDetails = MemberDetail::where('user_id', $user->id)->get();
                if (count($memberDetails) > 0) {
                    foreach ($memberDetails as $memberDetail) {
                        $memberDetail->delete();
                    }
                } else {
                    $member = MemberDetail::where('user_id', $user->id)->first();
                    if ($member) $member->delete();
                }
            } else if ($user->customRole->name == 'leader') {
                $leaderDetails = LeaderDetail::where('user_id', $user->id)->get();
                if (count($leaderDetails) > 0) {
                    foreach ($leaderDetails as $leaderDetail) {
                        $leaderDetail->delete();
                    }
                } else {
                    $leader = LeaderDetail::where('user_id', $user->id)->first();
                    if ($leader) $leader->delete();
                }
            } else if (($user->customRole->name == 'leader' ||  $user->customRole->name == 'member') && (count(MemberDetail::where('user_id', $user->id)->get()) > 0 || count(LeaderDetail::where('user_id', $user->id)->get()) > 0)) {
                // delete any other relations
                $memberDetails = MemberDetail::where('user_id', $user->id)->get();
                var_dump($memberDetails); die;
                if (count($memberDetails) > 0) {
                    foreach ($memberDetails as $memberDetail) {
                        $memberDetail->delete();
                    }
                } else {
                    $member = MemberDetail::where('user_id', $user->id)->first();
                    if ($member) $member->delete();
                }

                $leaderDetails = LeaderDetail::where('user_id', $user->id)->get();
                if (count($leaderDetails) > 0) {
                    foreach ($leaderDetails as $leaderDetail) {
                        $leaderDetail->delete();
                    }
                } else {
                    $leader = LeaderDetail::where('user_id', $user->id)->first();
                    if ($leader) $leader->delete();
                }
            }

            // var_dump($user->owns); die;
            if (count($user->hosts) > 0) {
                foreach ($user->hosts as $host) {
                    $host->delete();
                }
            } else if (count($user->owns) > 0) {
                foreach ($user->owns as $own) {
                    $own->delete();
                }
            }

            // delete user
            if ($user->delete()) {
                return $this->sendResponse(new UserResource($user), 'DELETE_SUCCESS', 204);
            } else {
                return $this->sendError('DELETE_FAILED');
            }

        } catch (\Throwable $th) {
            return $this->sendError('DELETE_FAILED', $th);
        }

        if ($user->delete()) {
            return $this->sendResponse(new UserResource($user), 'DELETE_SUCCESS', 204);
        } else {
            return $this->sendError('DELETE_FAILED');
        }
    }

    public function createMemberDetails($universityData, $collegeData, $departmentData, $degreeProgrammeData)
    {
        $university = University::where('name', $universityData)->first();
        if (is_null($university)) {
            $university = new University;
            $university->name = $universityData;
            $university->save();
        }
        
        $college = College::where('name', $college)->where('university_id', $university->id)->first();
        // var_dump($college); die;

        if (is_null($college)) {
            $college = new College;
            $college->name = $collegeData;
            $college->university_id = $university->id;
            $college->save();
        }

        $department = Department::where('name', $departmentData)->where('college_id', $college->id)->first();

        if (is_null($department)) {
            $department = new Department;
            $department->name = $departmentData;
            $department->college_id = $college->id;
            $department->save();
        }

        $degreeProgramme = DegreeProgramme::where('name', $degreeProgrammeData)->where('department_id', $department->id)->first();

        if (is_null($degreeProgramme)) {
            $degreeProgramme = new DegreeProgramme;
            $degreeProgramme->name = $degreeProgrammeData;
            $degreeProgramme->department_id = $department->id;
            $degreeProgramme->save();
        }

        return [
            'university' => $university,
            'college' => $college,
            'department' => $department,
            'degreeProgramme' => $degreeProgramme
        ];
    }
}
