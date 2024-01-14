<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
class UsersApisController extends Controller
{
    public function getUserDetails($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function checkIfUserExists($email)
    {
        $user = User::where('email', $email)->first();

        return response()->json(['exists' => !!$user]);
    }

    public function saveUserDetails(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Update user details based on the request data
        $user->update($request->all());

        return response()->json(['message' => 'User details updated successfully']);
    }
}
