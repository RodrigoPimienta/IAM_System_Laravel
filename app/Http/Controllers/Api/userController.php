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

    public function all(): object
    {
        $users = User::all($this->columns);
        return Controller::response(200, false, $message = 'User list', $users);
    }

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
