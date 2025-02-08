<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     description="Authenticate user and provide a token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                @OA\Property(property="password_confirmation", type="string", example="password123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example=""),
     *             @OA\Property(property="message", type="string", example="Login"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="token", type="string", example="example_token_here")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *             @OA\Property(property="message", type="string", example="Provided credentials are incorrect.")
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {

        $request = (object) $request->validate([
            "email"    => "required|email|exists:users",
            'password' => 'required|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || Hash::check($request->password, $user->password) == false) {
            return Controller::response(401, true, $message = 'Provided credentials are incorrect.');
        }

        // check if the user have status 1
        if ($user->status == 0) {
            return Controller::response(401, true, $message = 'User is inactive, please contact the administrator.');
        }

        // delete all tokens
        $user->tokens()->delete();
        $expiration  = now()->addMinutes(config('sanctum.expiration'));
        $token       = $user->createToken($user->email, ['*'], $expiration);
        $user->token = $token->plainTextToken;

        return Controller::response(200, true, $message = 'Login', $user);
    }
    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout user",
     *     description="Revoke the user's token",
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example=""),
     *             @OA\Property(property="message", type="string", example="Logout"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return Controller::response(200, false, $message = 'Logout');

    }

    /**
     * @OA\Get(
     *     path="/auth/permissions",
     *     tags={"Auth"},
     *     summary="Get user permissions",
     *     description="Retrieve the user's permissions and access",
     *     @OA\Response(
     *         response=200,
     *         description="Permissions retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example=""),
     *             @OA\Property(property="message", type="string", example="Permissions"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(property="permissions", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id_permission", type="integer", example=5),
     *                         @OA\Property(property="name", type="string", example="Edit Profile")
     *                     )
     *                 ),
     *                 @OA\Property(property="access", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="module", type="string", example="Users"),
     *                         @OA\Property(property="access_level", type="string", example="Read-Write")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *             @OA\Property(property="message", type="string", example="User not authenticated.")
     *         )
     *     )
     * )
     */
    public function permissions(Request $request)
    {
        $user              = $request->user()->select('id', 'name', 'email', 'status')->first();
        $user->permissions = $user->permissions()->get();
        $user->access      = $user->getAccess();
        unset($user->permissions);

        return Controller::response(200, false, $message = 'Permissions', $user);
    }
}
