<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request = (object) $request->validate([
            "email"    => "required|email|exists:users",
            'password' => 'required|confirmed',
        ]);

        $user = User::where('email', $request->email)->select('name','email')->first();


        if(!$user || Hash::check($request->password, $user->password)) {
            return Controller::response(401, true, $message = 'Provided credentials are incorrect.');
        }

        // check if the user have status 1
        if($user->status == 0){
            return Controller::response(401, true, $message = 'User is inactive, please contact the administrator.');
        }

        // delete all tokens
        $user->tokens()->delete();

        $token = $user->createToken($user->email);
        $user->token = $token->plainTextToken;

        return Controller::response(200, true, $message = 'Login', $user);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return Controller::response(200, false, $message = 'Logout');

    }
}
