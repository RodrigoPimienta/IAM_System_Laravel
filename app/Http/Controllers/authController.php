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

        $user = User::where('email', $request->email)->first();


        if(!$user || Hash::check($request->password, $user->password) == false){
            return Controller::response(401, true, $message = 'Provided credentials are incorrect.');
        }

        // check if the user have status 1
        if($user->status == 0){
            return Controller::response(401, true, $message = 'User is inactive, please contact the administrator.');
        }

        // delete all tokens
        $user->tokens()->delete();
        $expiration = now()->addMinutes(config('sanctum.expiration'));
        $token = $user->createToken($user->email,['*'], $expiration);
        $user->token = $token->plainTextToken;

        return Controller::response(200, true, $message = 'Login', $user);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return Controller::response(200, false, $message = 'Logout');

    }

    public function permissions(Request $request)
    {
        $user = $request->user()->select('id', 'name', 'email', 'status')->first();
        $user->permissions = $user->permissions()->get();
        $user->access = $user->getAccess();
        unset($user->permissions);

        return Controller::response(200, false, $message = 'Permissions', $user);
    }
}
