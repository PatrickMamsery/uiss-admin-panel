<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ClubResource;
use App\Http\Resources\ClubMembersResource;
use App\Http\Resources\ClubLeadersResource;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\ClubLead;
use App\Models\CustomRole;
use App\Models\User;

class ClubController extends BaseController
{
    public $per_page = 15;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendResponse(ClubResource::collection(Club::latest('updated_at')->paginate($this->per_page)), 'RETRIEVE_SUCCESS');
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
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // fail safe for duplicate club name
        $club = Club::where('name', $request->name)->first();
        if (!is_null($club)) {
            return $this->sendError('CREATE_FAILED', 'Club name already exists.');
        } else {
            $club = Club::create([
                'name' => $request->name,
                'description' => $request->description
            ]);

            if (is_null($club)) {
                return $this->sendError('CREATE_FAILED');
            } else {
                return $this->sendResponse(new ClubResource($club), 'CREATE_SUCCESS');
            }
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
        $club = Club::find($id);

        if (is_null($club)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            return $this->sendResponse(new ClubResource($club), 'RETRIEVE_SUCCESS');
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
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        $club = Club::find($id);

        if (is_null($club)) {
            return $this->sendError('UPDATE_FAILED');
        } else {
            $club->update($request->all());
            return $this->sendResponse(new ClubResource($club), 'UPDATE_SUCCESS');
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
        // check if club has members
        $club = Club::find($id);
        $members = $club->members;

        if (count($members) > 0) {
            return $this->sendError('DELETE_FAILED', 'Club has members.');
        } else {
            $club->delete();
            return $this->sendResponse(new ClubResource($club), 'DELETE_SUCCESS');
        }
    }

    /**
     * Get members belonging to a club
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getClubMembers($id)
    {
        $club = Club::findOrFail($id);

        if (is_null($club)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            $clubMembers = ClubMember::where('club_id', $club->id)->latest('updated_at')->paginate($this->per_page);

            return $this->sendResponse(ClubMembersResource::collection($clubMembers), 'RETRIEVE_SUCCESS');
        }
    }

    /**
     * Get club leaders
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     *
     */
    public function getClubLeaders($id)
    {
        $club = Club::findOrFail($id);

        if (is_null($club)) {
            return $this->sendError('RETRIEVE_FAILED');
        } else {
            $clubLeaders = ClubLead::where('club_id', $club->id)->latest('updated_at')->paginate($this->per_page);

            return $this->sendResponse(ClubLeadersResource::collection($clubLeaders), 'RETRIEVE_SUCCESS');
        }
    }

    public function addLeader(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if the user to be added is indeed a leader
        $role = CustomRole::where('name', 'leader')->first();
        $user = User::find($request->user_id);

        if ($user->role_id != $role->id) {
            return $this->sendError('UPDATE_FAILED', 'User is not a leader.');
        } else {
            $club = Club::find($id);

            if (is_null($club) || is_null($user)) {
                return $this->sendError('UPDATE_FAILED');
            } else {
                $clubLead = ClubLead::create([
                    'user_id' => $user->id,
                    'club_id' => $club->id
                ]);

                if (is_null($clubLead)) {
                    return $this->sendError('UPDATE_FAILED');
                } else {
                    return $this->sendResponse(new ClubLeadersResource($clubLead), 'UPDATE_SUCCESS');
                }
            }
        }
    }

    /**
     * Add a member to the club.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addMember(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        // check if the user to be added is indeed a member
        $role = CustomRole::where('name', 'member')->first();
        $user = User::find($request->user_id);

        if ($user->role_id != $role->id) {
            return $this->sendError('UPDATE_FAILED', 'User is not a member.');
        } else {
            $club = Club::find($id);

            if (is_null($club) || is_null($user)) {
                return $this->sendError('UPDATE_FAILED');
            } else {
                $club->members()->attach($user);
                return $this->sendResponse(new ClubResource($club), 'UPDATE_SUCCESS');
            }
        }
    }

    /**
     * Register to club
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     *
     */
    public function registerToClub(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('VALIDATION_ERROR', $validator->errors());
        }

        $club = Club::find($id);
        $user = User::where('name', $request->name)->first();

        if (is_null($user)) {
            $user = User::create([
                'name' => $request->name,
                'email' => strtolower(preg_replace('/\s+/', '', $request->name)) . '@example.com',
                'role_id' => 5,
                'password' => bcrypt($request->name)
            ]);
        }

        if (is_null($club) || is_null($user)) {
            return $this->sendError('CREATE_FAILED');
        } else {
            // create a new club member
            $clubMember = ClubMember::firstOrCreate([
                'user_id' => $user->id,
                'club_id' => $club->id
            ]);

            return $this->sendResponse(new ClubMembersResource($clubMember), 'CREATE_SUCCESS');
        }
    }
}
