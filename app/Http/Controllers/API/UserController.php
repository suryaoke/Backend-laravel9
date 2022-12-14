<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request)
    {

        try {

            //TODO: validate request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // TODO: find user by email
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Unauthorized', 401);
            }
            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new Exception('Invalid password');
            }

            // TODO: Generate token
            $toketResult = $user->createToken('authToken')->plainTextToken;

            // TODO: Return response.
            return ResponseFormatter::success([
                'access_token' => $toketResult,
                'token_type' => 'Bearer',
                'user' => $user,

            ], 'Login success');
        } catch (Exception $error) {
            return ResponseFormatter::error('Authication Failed');
        }
    }

    public function register(Request $request)
    {
        try {
            // TODO: validate request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new  Password],
            ]);

            // TODO: Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // TODO: Cerate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // TODO: Return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register success');
        } catch (\Throwable $error) {
            // TODO: Retrun error response
            return ResponseFormatter::error($error->getMessage());
        }
    }
    public function Logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Logout success');
    }


    public function fetch(Request $request)
    {
        $user = $request->user();
        return ResponseFormatter::success($user, 'Logout success');
    }
}
