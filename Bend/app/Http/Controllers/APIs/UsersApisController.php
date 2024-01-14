<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
class UsersApisController extends Controller
{
    public function getUserDetails()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        if (!$user) {
            // Handle case where user is not authenticated
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Do something with the user details
        // ...

        return response()->json(['data' => $user]);
    }

    public function getAllUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function checkIfUserExists($search)
    {
        $users = User::where('email', 'like', '%' . $search . '%')
            ->orWhere('firstName', 'like', '%' . $search . '%')
            ->orWhere('lastName', 'like', '%' . $search . '%')
           ->get();
        // $user = User::where('email', 'like', '%' . $search . '%')
        //     ->orWhere('firstName', 'like', '%' . $search . '%')
        //     ->orWhere('lastName', 'like', '%' . $search . '%')
        //    ->first();

        // return response()->json(['exists' => !!$user]);
        return response()->json(['data' => $users]);
    }

    public function saveUserDetails(Request $request)
    {
        $user = User::find(Auth::user()->id);

        if (!$user) {
            // Handle case where user is not authenticated
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Update user details based on the request data
        $user->update($request->has('password') ? $request->all() : $request->except(['password']));


        return response()->json(['message' => 'User details updated successfully']);
    }
}
