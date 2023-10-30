<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\MemberResource;
use App\Http\Resources\LeaderResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\CustomRole as Role;

/**
 * @group Member management
 *
 * APIs for managing members
 */
class MemberController extends BaseController
{
    /**
     * Get all members by role
     *
     * This endpoint retrieves all members as organised by their roles whether admin or member.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsersByRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();

        if(!$role) {
            return $this->sendError('ROLE_NOT_FOUND');
        }

        if ($role->name == 'member') {
            $users = User::with('memberDetails')->whereHas('customRole', function ($q) {
                $q->where('name', 'member');
            })->latest('updated_at')->paginate();

            return $this->sendResponse(MemberResource::collection($users), 'RETRIEVE_SUCCESS');
        } else if ($role->name == 'leader') {
            $users = User::with('leaderDetails')->whereHas('customRole', function ($q) {
                $q->where('name', 'leader');
            })->latest('updated_at')->paginate();

            return $this->sendResponse(LeaderResource::collection($users), 'RETRIEVE_SUCCESS');
        }elseif ($role->name == 'admin') {
            $users = User::whereHas('customRole', function ($q) {
                $q->where('name', 'admin');
            })->latest('updated_at')->paginate();

            return $this->sendResponse(UserResource::collection($users), 'RETRIEVE_SUCCESS');
        } elseif ($role->name == 'developer') {
            $users = User::whereHas('customRole', function ($q) {
                $q->where('name', 'developer');
            })->latest('updated_at')->paginate();

            return $this->sendResponse(UserResource::collection($users), 'RETRIEVE_SUCCESS');
        } else {
            return $this->sendError('ROLE_NOT_FOUND');
        }
    }
}
