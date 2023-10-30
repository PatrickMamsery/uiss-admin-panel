<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Position;

use App\Models\CustomRole;
use App\Models\University;
use App\Models\College;
use App\Models\Department;
use App\Models\DegreeProgramme;
use App\Models\LeaderDetail;
use App\Models\MemberDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\UserResource;
use App\Http\Resources\MemberResource;
use App\Http\Resources\LeaderResource;

/**
 * @group User Management
 */
class UserController extends BaseController
{
    /**
     * GET (GET api/users)
     *
     * Retrieves all users paginated in chunks of 15
     *
     * @authenticated
     *
     * @queryParam page The page number to retrieve. Example: 1
     *
     * @response scenario="success" {
     *  "data": [
     *     {
     *          "id": 1,
     *          "name": "John Doe",
     *          "email": "admin@admin.com",
     *          "phone": "08012345678",
     *          "image": "https://via.placeholder.com/150",
     *      }
     *  ],
     *  "meta": {
     *      "current_page": 1,
     *      "from": 1,
     *      "last_page": 1,
    *       "path": "http://localhost:8000/api/users",
    *       "per_page": 15,
    *       "to": 1,
    *       "total": 1
    *   },
    *   "status": "success",
    *   "message": "Resource retrieved successfully",
    *   "statusCode": 200
     * }
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $per_page = 15;
        return $this->sendResponse(UserResource::collection(User::paginate($per_page)), 'RETRIEVE_SUCCESS');
    }

    /**
     * POST api/users
     *
     * Creates a new user
     *
     * @authenticated
     * @group User Management
     *
     * @bodyParam name string required The name of user
     * @bodyParam email string required Email of the user, should be valid email, unique to the users table
     * @bodyParam phone string Phone number of the user, unique to the users table
     * @bodyParam image string Image of the user, should be a valid url
     * @bodyParam role string Role of the user, should be either "member", "leader", "developer" or "admin"
     * @bodyParam additionalInfo object Additional information of the user, should be an object with the following keys:
     * - position: string, required if role is "leader"
     * - university: string, required if role is "member"
     * - college: string, required if role is "member"
     * - department: string, required if role is "member"
     * - degreeProgramme: string, required if role is "member"

     * @response scenario="member" {
     *   "data": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "johndoe@mail.com",
     *       "phone": "08012345678",
     *       "image": "https://via.placeholder.com/150",
     *       "role": "member",
     *       "regNo": "2020-04-09890",
     *       "isProjectOwner": 0,
     *       "areaOfInterest": "Software Development",
     *       "initialAreaOfInterest": "Software Development - 2020",
     *       "university": "University of Lagos",
     *       "college": "College of Medicine",
     *       "department": "Department of Surgery",
     *       "degreeProgramme": "MBBS"
     *   },
     *   "status": "success",
     *   "message": "Resource created successfully",
     *   "statusCode": 200
     *   }

     * @response scenario="leader" {
     *   "data": {
     *       "id": 1,
     *       "name": "John Doe",
     *       "email": "johndoe@mail.com",
     *       "phone": "08012345678",
     *       "image": "https://via.placeholder.com/150",
     *       "role": "leader",
     *       "isProjectOwner": 0,
     *       "position": "President"
     *   },
     *   "status": "success",
     *   "message": "Resource created successfully",
     *   "statusCode": 200
     *   }
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|unique:users',
            'image' => 'nullable|string',
            'role' => 'nullable|string',
            'additionalInfo' => 'nullable',
        ]);


        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if there's role attribute else create a new user with "member" role
        if ($request->has('role')) {
            // clean the role input to small letters
            $inputRole = strtolower($request->role);

            // check if the role exists
            $role = CustomRole::where('name', $inputRole)->first();

            if (is_null($role)) {
                $role = new CustomRole;
                $role->name = $inputRole;
                $role->save();
            }

            // check if there is additional info in the payload
            if ($role && !$request->has('additionalInfo')) {
                return $this->sendError('MISSING_ADDITIONAL_INFO');
            } else {
                // check if additional info data is correct for given role
                $additionalInfoData = $request->additionalInfo;

                if ($role->name == 'member' && array_key_exists('position', $additionalInfoData)) {
                    return $this->sendError('ADDITIONAL_INFO_ROLE_MISMATCH');
                } else if ($role->name == 'leader' && array_key_exists('university', $additionalInfoData)) {
                    return $this->sendError('ADDITIONAL_INFO_ROLE_MISMATCH');
                } else if ($role->name == 'leader' && !array_key_exists('position', $additionalInfoData)) {
                    return $this->sendError('ADDITIONAL_INFO_ROLE_MISMATCH');
                }

                // create the user
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->role_id = $role->id;
                $user->password = $request->phone ? bcrypt($request->phone) : bcrypt($request->email);
                $user->image = $request->image ? $request->image : null;
                // $user->save();

                // implement try-catch block so as to prevent any error from disrupting normal procedure

                try {
                    DB::transaction(function () use ($user, $role, $additionalInfoData, $request) {
                        $user->save();

                        switch ($role->name) {
                            case ('member'):
                                // create member details, that is, university, college, department and degreeProgramme entries in DB

                                try {
                                    $data = $this->createMemberDetails($additionalInfoData['university'], $additionalInfoData['college'], $additionalInfoData['department'], $additionalInfoData['degreeProgramme']);
                                } catch (\Exception $error) {
                                    $this->sendError('CREATE_FAILED', $error);
                                }

                                // create private data for the member details
                                $university = $data['university'];
                                $college = $data['college'];
                                $department = $data['department'];
                                $degreeProgramme = $data['degreeProgramme'];

                                $memberDetails = MemberDetail::where('user_id', $user->id)->first();
                                // var_dump($memberDetails); die;

                                if (
                                    !is_null($memberDetails)
                                    && $memberDetails->reg_no == $additionalInfoData['regNo']
                                    && $memberDetails->area_of_interest == $additionalInfoData['areaOfInterest']
                                    && $memberDetails->university_id == $university->id
                                    && $memberDetails->college_id == $college->id
                                    && $memberDetails->department_id == $department->id
                                    && $memberDetails->degree_programme_id == $degreeProgramme->id
                                ) {
                                    return $this->sendError('DUPLICATE_ENTRY');
                                } else {
                                    // var_dump($university->id, $department->id, $college->id, $degreeProgramme->id, $additionalInfoData['regNo'], $additionalInfoData['areaOfInterest']); die;
                                    $member = new MemberDetail;
                                    $member->user_id = $user->id;
                                    $member->reg_no = $additionalInfoData['regNo'];
                                    $member->area_of_interest = $additionalInfoData['areaOfInterest'];
                                    $member->initial_area_of_interest = $additionalInfoData['initialAreaOfInterest'];
                                    $member->university_id = $university->id;
                                    $member->college_id = $college->id;
                                    $member->department_id = $department->id;
                                    $member->degree_programme_id = $degreeProgramme->id;
                                    $member->save();

                                    // redundant typecasting

                                    // if ($member->save()) {
                                    //     // $createdUser = User::with('memberDetails', 'customRole')->find($user->id);
                                    //     // var_dump($createdUser); die;
                                    //     // return $this->sendResponse(new MemberResource($createdUser), 'CREATE_SUCCESS');
                                    // } else {
                                    //     return $this->sendError('CREATE_FAILED', 'Miscellaneous error, recheck your data entries');
                                    // }
                                }
                                break;

                            case ('leader'):
                                $position = Position::where('title', $additionalInfoData['position'])->first();

                                if (is_null($position)) {
                                    $position = new Position;
                                    $position->title = $additionalInfoData['position'];

                                    $position->save();
                                }

                                $leader = new LeaderDetail;
                                $leader->user_id = $user->id;
                                $leader->position_id = $position->id;
                                $leader->start_date = array_key_exists('startDate', $additionalInfoData) ? $additionalInfoData['startDate'] : \Carbon\Carbon::now();
                                $leader->end_date = array_key_exists('endDate', $additionalInfoData) ? $additionalInfoData['endDate'] : \Carbon\Carbon::now()->addYear();

                                $leader->save();

                                // var_dump($user->leaderDetails); die;
                                // return $this->sendResponse(new LeaderResource($user), 'CREATE_SUCCESS');
                                break;

                                // default:
                                //     return $this->sendError('CREATE_FAILED', 'Miscellaneous error, recheck your data entries');
                                // break;
                        }
                    });

                    // formulate a return response methodology based on role
                    if ($role->name == 'member') {
                        $createdUser = User::with('memberDetails', 'customRole')->find($user->id);
                        return $this->sendResponse(new MemberResource($createdUser), 'CREATE_SUCCESS');
                    } else if ($role->name == 'leader') {
                        $createdUser = User::with('leaderDetails', 'customRole')->find($user->id);
                        return $this->sendResponse(new LeaderResource($createdUser), 'CREATE_SUCCESS');
                    }
                } catch (\Throwable $th) {
                    return $this->sendError('CREATE_FAILED', $th->getMessage());
                }
            }
        } else {
            // creating a new user entry with "member" role
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role_id = CustomRole::where('name', 'member')->first()->id;
            $user->password = $request->phone ? bcrypt($request->phone) : bcrypt($request->email);
            $user->image = $request->image ?? NULL;
            $user->save();

            if (is_null($user)) {
                return $this->sendError('CREATE_FAILED');
            } else {
                return $this->sendResponse(new UserResource($user), 'CREATE_SUCCESS');
            }
        }
    }

    /**
     * GET /api/users/{id}
     *
     * Display the specified user
     *
     * @response scenario="success" {
     *  "data": {
     *     "id": 1,
     *     "name": "Admin",
     *     "email": "admin@admin.com",
     *     "phone": "08012345678",
     *     "image": "https://via.placeholder.com/150",
     *     },
     *     "status": "success",
     *     "message": "Resource retrieved successfully",
     *     "statusCode": 200
     * }
     *
     * @response status=404 scenario="not found" {
     * "status": "error",
     * "message": "Resource not found",
     * "statusCode": 404
     * }
     *
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
            case ('member'):
                $user = User::with('memberDetails')->find($id);

                return $this->sendResponse(new MemberResource($user), 'RETRIEVE_SUCCESS');
                break;

            case ('leader'):
                $user = User::with('leaderDetails')->find($id);

                return $this->sendResponse(new LeaderResource($user), 'RETRIEVE_SUCCESS');
                break;
        }

        return $this->sendResponse(new UserResource($user), 'RETRIEVE_SUCCESS');
    }

    /**
     * PUT /api/users/{id}
     *
     * Update the specified user
     *
     * @bodyParam name string The name of the user. Example: John Doe
     * @bodyParam email string The email of the user. Example: johndoe@mail.com
     * @bodyParam phone string The phone number of the user. Example: 08012345678
     * @bodyParam image string The image of the user. Example: https://res.cloudinary.com/duqkqzjxk/image/upload/v1590000000/avatars/1.jpg
     * @bodyParam role string The role of the user. Example: member
     * @bodyParam additionalInfo array The additional information of the user. Example: {"regNo": "123456", "areaOfInterest": "Software Engineering", "university": "University of Lagos", "college": "College of Engineering", "department": "Department of Computer Science", "degreeProgramme": "B.Sc. Computer Science"}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

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

        // check if there's role attribute else create a new user with "member" role
        if ($request->has('role')) {
            // clean the role input to small letters
            $inputRole = strtolower($request->role);

            // check if the role exists
            $role = CustomRole::where('name', $inputRole)->first();

            if (is_null($role)) {
                $role = new CustomRole;
                $role->name = $inputRole;
                $role->save();
            }

            // check if there is additional info in the payload
            if ($role && !$request->has('additionalInfo')) {
                return $this->sendError('MISSING_ADDITIONAL_INFO');
            } else {
                // check if additional info data is correct for given role
                $additionalInfoData = $request->additionalInfo;

                if ($role->name == 'member' && array_key_exists('position', $additionalInfoData)) {
                    return $this->sendError('ADDITIONAL_INFO_ROLE_MISMATCH');
                } else if ($role->name == 'leader' && array_key_exists('university', $additionalInfoData)) {
                    return $this->sendError('ADDITIONAL_INFO_ROLE_MISMATCH');
                } else if ($role->name == 'leader' && !array_key_exists('position', $additionalInfoData)) {
                    return $this->sendError('ADDITIONAL_INFO_ROLE_MISMATCH');
                }

                // update the user's data
                $user->name = $request->name ? $request->name : $user->name;
                $user->email = $request->email ? $request->email : $user->email;
                $user->phone = $request->phone ? $request->phone : $user->phone;
                $user->role_id = $role->id;
                $user->image = $request->image ? $request->image : $user->image;

                // implement try-catch block to prevent any unforeseen error
                try {
                    DB::transaction(function () use ($user, $role, $additionalInfoData, $request) {
                        $user->save();

                        switch ($role->name) {
                            case ('member'):
                                // create member details, that is, university, college, department and degreeProgramme entries in DB
                                $data = $this->createMemberDetails($additionalInfoData['university'], $additionalInfoData['college'], $additionalInfoData['department'], $additionalInfoData['degreeProgramme']);

                                // create private data for the member details
                                $university = $data['university'];
                                $college = $data['college'];
                                $department = $data['department'];
                                $degreeProgramme = $data['degreeProgramme'];

                                $memberDetails = MemberDetail::where('user_id', $user->id)->first();

                                if (
                                    !is_null($memberDetails)
                                    && $memberDetails->reg_no == $additionalInfoData['regNo']
                                    && $memberDetails->area_of_interest == $additionalInfoData['areaOfInterest']
                                    && $memberDetails->university_id == $university->id
                                    && $memberDetails->college_id == $college->id
                                    && $memberDetails->department_id == $department->id
                                    && $memberDetails->degree_programme_id == $degreeProgramme->id
                                ) {
                                    $memberDetails->user_id = $user->id;
                                    $memberDetails->reg_no = $additionalInfoData['regNo'] ? $additionalInfoData['regNo'] : $memberDetails->reg_no;
                                    $memberDetails->area_of_interest = $additionalInfoData['areaOfInterest'] ? $additionalInfoData['areaOfInterest'] : $memberDetails->area_of_interest;
                                    $memberDetails->initial_area_of_interest = $additionalInfoData['initialAreaOfInterest'] ? $additionalInfoData['initialAreaOfInterest'] : $memberDetails->initial_area_of_interest;
                                    $memberDetails->university_id = $university ? $university->id : $memberDetails->university_id;
                                    $memberDetails->department_id = $department ? $department->id : $memberDetails->department_id;
                                    $memberDetails->college_id = $college ? $college->id : $memberDetails->college_id;
                                    $memberDetails->degree_programme_id = $degreeProgramme ? $degreeProgramme->id : $memberDetails->degree_programme_id;
                                    $memberDetails->save();

                                    return $this->sendResponse(new MemberResource($user), 'CREATE_SUCCESS');
                                } else {
                                    $member = new MemberDetail;
                                    $member->user_id = $user->id;
                                    $member->reg_no = $request->additionalInfo['regNo'];
                                    $member->area_of_interest = $request->additionalInfo['areaOfInterest'];
                                    $member->initial_area_of_interest = $request->additionalInfo['initialAreaOfInterest'];
                                    $member->university_id = $university->id;
                                    $member->department_id = $department->id;
                                    $member->college_id = $college->id;
                                    $member->degree_programme_id = $degreeProgramme->id;
                                    $member->save();

                                    // return $this->sendResponse(new MemberResource($user), 'CREATE_SUCCESS');
                                }
                                break;

                            case ('leader'):
                                $position = Position::where('title', $additionalInfoData['position'])->first();
                                // var_dump($position); die;

                                if (is_null($position)) {
                                    $position = new Position;
                                    $position->title = $additionalInfoData['position'];

                                    $position->save();
                                }

                                // find if there's is any entry for this user in the leader details table
                                $leaderDetails = LeaderDetail::where('user_id', $user->id)->first();

                                if (!is_null($leaderDetails)) {
                                    $leaderDetails->user_id = $user->id;
                                    $leaderDetails->position_id = $position ? $position->id : $leaderDetails->position_id;
                                    $leaderDetails->start_date = array_key_exists('startDate', $additionalInfoData) ? $additionalInfoData['startDate'] : \Carbon\Carbon::now();
                                    $leaderDetails->end_date = array_key_exists('endDate', $additionalInfoData) ? $additionalInfoData['endDate'] : \Carbon\Carbon::now()->addYear();

                                    $leaderDetails->save();
                                } else {
                                    $leader = new LeaderDetail;
                                    $leader->user_id = $user->id;
                                    $leader->position_id = $position->id;
                                    $leader->start_date = array_key_exists('startDate', $additionalInfoData) ? $additionalInfoData['startDate'] : \Carbon\Carbon::now();
                                    $leader->end_date = array_key_exists('endDate', $additionalInfoData) ? $additionalInfoData['endDate'] : \Carbon\Carbon::now()->addYear();

                                    $leader->save();
                                }

                                // return $this->sendResponse(new LeaderResource($user), 'CREATE_SUCCESS');
                                break;

                            default:
                                return $this->sendError('UPDATE_FAILED', 'Miscellaneous error, recheck your data entries');
                                break;
                        }
                    });

                    // formulate a return response methodology based on role
                    if ($role->name == 'member') {
                        $createdUser = User::with('memberDetails', 'customRole')->find($user->id);
                        return $this->sendResponse(new MemberResource($createdUser), 'CREATE_SUCCESS');
                    } else if ($role->name == 'leader') {
                        $createdUser = User::with('leaderDetails', 'customRole')->find($user->id);
                        return $this->sendResponse(new LeaderResource($createdUser), 'CREATE_SUCCESS');
                    }
                } catch (\Throwable $th) {
                    return $this->sendError('UPDATE_FAILED', $th->getMessage());
                }
            }
        } else {
            // check if the user had previous role set to member or leader
            if ($user->customRole->name == 'member' || $user->customRole->name == 'leader') {
                return $this->sendError('UPDATE_FAILED', 'Missing role data, essential for this action');
            } else {
                $user->name = $request->name ? $request->name : $user->name;
                $user->email = $request->email ? $request->email : $user->email;
                $user->phone = $request->phone ? $request->phone : $user->phone;
                $user->role_id = $user->role_id;
                $user->image = $request->image ? $request->image : $user->image;
                $user->save();

                if (is_null($user)) {
                    return $this->sendError('UPDATE_FAILED');
                } else {
                    return $this->sendResponse(new UserResource($user), 'UPDATE_SUCCESS');
                }
            }
        }
    }

    /**
     * DELETE /api/users/{id}
     *
     * Delete a user from the database completely and all its relations
     *
     * <aside class="notice"> <strong>NOTE:</strong> This action is irreversible </aside>
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
                var_dump($memberDetails);
                die;
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
        // var_dump("in create member method"); die;
        // implement try-catch block to get the error
        try {
            $university = University::where('name', $universityData)->first();
            if (is_null($university)) {
                $university = new University;
                $university->name = $universityData;
                $university->save();
            }

            $college = College::where('name', $collegeData)->where('university_id', $university->id)->first();
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

            // var_dump("here then"); die;

            $data = [
                'university' => $university,
                'college' => $college,
                'department' => $department,
                'degreeProgramme' => $degreeProgramme
            ];

            return $data;
        } catch (\Throwable $th) {
            return $this->sendError('CREATE_FAILED', $th);
        }
    }
}
