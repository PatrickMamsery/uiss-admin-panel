<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @group User Authentication
 */
class PassportAuthController extends Controller
{
    /**
     * CREATE NEW USER BY REGISTRATION (POST api/register)
     *
     * This endpoint enables user to register themselves to the application and at the same time authenticates them on successful registration
     *
     * @unauthenticated
     *
     * @group User Authentication
     *
     * @bodyParam name string required The name of user
     * @bodyParam email string required Email of the user, should be valid email, unique to the users table
     * @bodyParam password string required Must be at least 6 characters
     * @bodyParam password_confirmation string required Must be same to the password value
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->save();

        $token = $user->createToken('AuthToken')->accessToken;

        return response(['token' => $token]);
    }

    /**
     * LOGIN (POST api/login)
     *
     * Logs-in user(s) to the specified dashboard
     *
     * @unauthenticated
     *
     * @group User Authentication
     *
     * @bodyParam email string required Email of the user, should be valid email, unique to the users table
     * @bodyParam password string required Must be at least 6 characters
     *
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('AuthToken')->accessToken;

            return response(
                [
                    'token' => $token,
                    'userData' => [
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'phone' => Auth::user()->phone,
                        'image' => Auth::user()->image,
                        'role' => Auth::user()->customRole->name,
                    ]
                ], 200
            );
        }

        return response(['error' => 'Unauthorized'], 401);
    }

    /**
     * LOGOUT (POST api/logout)
     *
     * Logs-out user(s) from the specified dashboard
     *
     * @authenticated
     *
     * @response {
     *  "message": "Successfully logged out"
     * }
     */

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response(['message' => 'Successfully logged out'], 200);
    }
}
