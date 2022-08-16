<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // dd($request->all());
        // Get validation rules
        $validate = $this->registration_rules($request);

        // Run validation
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors(),
                'status' => 400,
            ]);
        }

        $user = new User();

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        try {
            $user->save();
            // $user->create($request, [
            //     'first_name' => $request->first_name,
            //     'last_name' => $request->last_name,
            //     'username' => $request->username,
            //     'email' => $request->email,
            //     'password' => $request->email,
            // ]);

            return response()->json([
                'success' => true,
                'message' => 'Signup Successful',
                'status' => 200,
                'user' => $user,
            ]);
        } catch (\Throwable$th) {
            Log::error($th);
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Oops! Something went wrong. Try Again!',
            ]);
        }

    }

    /**
     * Signup Validation Rules
     * @return object The validator object
     */
    public function registration_rules(Request $request)
    {
        // Make and return validation rules
        return Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email|max:50',
            'username' => 'required|string|min:4|max:15|unique:users,username',
            'password' => 'required|min:8|max:30|string|confirmed',
        ]);
    }

}
