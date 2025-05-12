<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
// use hash
use Illuminate\Support\Facades\Hash;
// user model
use App\Models\User;
class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }


        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
    public function logout(RegisterRequest $request)
    {
        auth()->logout();
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful'], 200);
    }
    public function register(RegisterRequest $request)
    {
        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'profile_image' => $profileImage,
            'phone' => $request->phone,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        /* $user->sendEmailVerificationNotification(); */
        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
   /*  public function register(Request $request)
    {
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'User registered successfully', 'user'=> $user, 'token'=>$token], 201);
    } */
}
