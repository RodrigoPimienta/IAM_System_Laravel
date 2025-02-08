<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class userController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['']),
        ];
    }

    public function index()
    {
        return 'ok';
    }

    private $columns = [
        'id',
        'name',
        'email',
        'status',
    ];

/**
 * @OA\Get(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Get list of users",
 *     description="Returns list of users",
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="User list"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="John Doe"),
 *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                     @OA\Property(property="status", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Bad request"),
 *             @OA\Property(property="message", type="string", example="Invalid input data"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function all(): object
    {
        $users = User::all($this->columns);
        return Controller::response(200, false, $message = 'User list', $users);
    }

/**
 * @OA\Post(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Create a new user",
 *     description="Creates a new user and assigns a profile",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", example="password123"),
 *             @OA\Property(property="id_profile", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User created",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="User created"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Bad request"),
 *             @OA\Property(property="message", type="string", example="Error creating user"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


    public function store(Request $request): object
    {
        $request = (object) $request->validate([
            'name'                  => 'required|max:255',
            "email"                 => "required|email|unique:users",
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',
            'id_profile'            => 'nullable|int|exists:profiles,id_profile',
        ]);

        DB::beginTransaction();

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'created_at' => now(),
        ]);

        if (! $user) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating user');
        }

        // create profile
        $profileUser = ProfileUser::create([
            'id_profile' => $request->id_profile,
            'id_user'    => $user->id_user,
            'created_at' => now(),
        ]);

        if (! $profileUser) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating user profile');
        }

        DB::commit();
        return Controller::response(200, true, $message = 'User created', $user);
    }

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Get user details",
 *     description="Returns the details of a specific user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="User found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


    public function show(int $id): object
    {
        $user = User::find($id, $this->columns);
//dd($user->profile);

        if (! $user) {
            return Controller::response(404, true, $message = 'User not found');
        }

        $user->load('profile');

        return Controller::response(200, false, $message = 'User found', $user);
    }

   /**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Update user information",
 *     description="Updates the user's details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="John Doe Updated"),
 *             @OA\Property(property="email", type="string", example="john.doe.updated@example.com"),
 *             @OA\Property(property="id_profile", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="User updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe Updated"),
 *                 @OA\Property(property="email", type="string", example="john.doe.updated@example.com"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


    public function update(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'name'       => 'required|max:255',
            'email'      => [
                'required',
                'email',
                Rule::unique('users')->ignore($id),
            ],
            'id_profile' => 'nullable|int|exists:profiles,id_profile',
        ]);

        $user = User::find($id, $this->columns);

        if (! $user) {
            return Controller::response(404, true, $message = 'User not found');
        }

        DB::beginTransaction();

        $user->name  = $request->name;
        $user->email = $request->email;

        if (! $user->save()) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error updating user');
        }

        // update to status 0 for all profiles

        $update = ProfileUser::where('id_user', $user->id_user)->update(['status' => 0]);

        if ($update === false) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error updating user profile');
        }

        // create profile
        $profileUser = ProfileUser::create([
            'id_profile' => $request->id_profile,
            'id_user'    => $id,
            'created_at' => now(),
        ]);

        if (! $profileUser) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating user profile');
        }

        DB::commit();
        return Controller::response(200, false, $message = 'User updated', $user);
    }

   /**
 * @OA\Patch(
 *     path="/api/users/{id}/status",
 *     tags={"Users"},
 *     summary="Update user status",
 *     description="Updates the user's status (active/inactive)",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User status updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="User status updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


    public function updateStatus(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'status' => 'required|int|in:0,1',
        ]);

        $user = User::find($id, $this->columns);

        if (! $user) {
            return Controller::response(404, true, $message = 'User not found');
        }

        $user->status = $request->status;

        if (! $user->save()) {
            return Controller::response(400, true, $message = 'Error updating user status');
        }

        return Controller::response(200, false, $message = 'User status updated', $user);
    }

  /**
 * @OA\Patch(
 *     path="/api/users/{id}/password",
 *     tags={"Users"},
 *     summary="Update user password",
 *     description="Updates the user's password",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="password", type="string", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", example="newpassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User password updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="User password updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="User not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function updatePassword(Request $request, int $id): object
    {
        $authenticatedUser = $request->user();
        if ($authenticatedUser->id_user != $id) {
            return Controller::response(403, true, $message = 'Unauthorized user');
        }

        // if (!$authenticatedUse
        $request = (object) $request->validate([
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::find($id, $this->columns);

        if (! $user) {
            return Controller::response(404, true, $message = 'User not found');
        }

        $user->password = bcrypt($request->password);

        if (! $user->save()) {
            return Controller::response(400, true, $message = 'Error updating user password');
        }

        return Controller::response(200, false, $message = 'User password updated', $user);
    }
}
