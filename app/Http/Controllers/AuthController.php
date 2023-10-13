<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Sign up user
    public function register(Request $request)
    {
        // Field validation
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed'
        ]);

        // Create user
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password'])
        ]);

        // Return user & tkn in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ]);
    }

    // Sign in user
    public function login(Request $request)
    {
        // Field validation
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // Attempt signin
        if(!Auth::attempt($attrs))
        {
            return response([
                'message' => 'Invalid email or password. Please try again.'
            ], 403);
        }

        // Return user & tkn in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    // Sign out user
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Thank you for using VibeVerse! You’re now signed out. See you again soon!'
        ], 200);
    }

    // Get user details
    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }

    // Update user
    public function update(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required|string'
        ]);

        $image = $this->saveImage($request->$image, 'profiles');

        auth()->user()->update([
            'message' => 'Account was updated!',
            'user' => auth()->user()
        ], 200);
    }
}
