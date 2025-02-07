<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class userController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: [''])
        ];
    }

    public function index(){
        return 'ok';
    }

    private $columns = [
        'id_user',
        'name',
        'email',
        'status',
    ];


    public function all():object{
        $users = User::all($this->columns);
        return Controller::response(200, false, $message = 'User list', $users);
    }

    public function store(Request $request):object {
        $request = (object) $request->validate([
            'name' => 'required|max:255',
            "email"    => "required|email|unique:users",
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password),
            'created_at'=> now(),
        ]);

        if(! $user){
            return Controller::response(400, true, $message = 'Error creating user');
        }

        return Controller::response(200, true, $message='User created', $user);
    }

    public function show(int $id):object {
        $user = User::find($id, $this->columns);
        if(! $user){
            return Controller::response(404, true, $message = 'User not found');
        }

        return Controller::response(200, false, $message = 'User found', $user);
    }


    public function update(Request $request, int $id):object {
        $request = (object) $request->validate([
            'name' => 'required|max:255',
            "email"    => "required|email|unique:users",
        ]);

        $user = User::find($id, $this->columns);

        if(! $user){
            return Controller::response(404, true, $message = 'User not found');
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if(! $user->save()){
            return Controller::response(400, true, $message = 'Error updating user');
        }


        return Controller::response(200, false, $message = 'User updated', $user);
    }

    public function updateStatus(Request $request, int $id):object {
        $request = (object) $request->validate([
            'status' => 'required|int|in:0,1',
        ]);

        $user = User::find($id, $this->columns);

        if(! $user){
            return Controller::response(404, true, $message = 'User not found');
        }

        $user->status = $request->status;

        if(! $user->save()){
            return Controller::response(400, true, $message = 'Error updating user status');
        }

        return Controller::response(200, false, $message = 'User status updated', $user);
    }

    public function updatePassword(Request $request, int $id):object {
        $request = (object) $request->validate([
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = User::find($id, $this->columns);

        if(! $user){
            return Controller::response(404, true, $message = 'User not found');
        }

        $user->password = bcrypt($request->password);

        if(! $user->save()){
            return Controller::response(400, true, $message = 'Error updating user password');
        }

        return Controller::response(200, false, $message = 'User password updated', $user);
    }
}

