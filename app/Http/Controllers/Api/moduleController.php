<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class moduleController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: [''])
        ];
    }

    private $colums = [
        'id_module',
        'name',
        'key',
        'status',
    ];

    public function index(){
        return 'ok';
    }

    public function all(): object{
        $modules = Module::all($this->colums);
        return Controller::response(200, false, $message = 'Module list', $modules);
    }

    public function store(Request $request): object{
        $request = (object) $request->validate([
            'name' => 'required|max:255',
            'key' => 'required|max:255|unique:modules,key',
        ]);

        $module = Module::create([
            'name' => $request->name,
            'key' => $request->key,
        ]);

        if(! $module){
            return Controller::response(400, true, $message = 'Error creating module');
        }

        return Controller::response(201, false, $message = 'Module created', $module);
    }

    public function show(int $id): object{
        $module = Module::find($id, $this->colums);
        if(! $module){
            return Controller::response(404, true, $message = 'Module not found');
        }

        return Controller::response(200, false, $message = 'Module found', $module);
    }

    public function update(Request $request, int $id): object{
        $request = (object) $request->validate([
            'name' => 'required|max:255',
            'key' => 'required|max:255',
        ]);

        $module = Module::find($id, $this->colums);
        if(! $module){
            return Controller::response(404, true, $message = 'Module not found');
        }

        $module->name = $request->name;
        $module->key = $request->key;

        $module->save();

        return Controller::response(200, false, $message = 'Module updated', $module);
    }

    public function updateStatus(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'status'=> ['required','int','in:0,1'],
        ]);

        $module = Module::find($id, $this->colums);
        if(! $module){
            return Controller::response(404, true, $message = 'Module not found');
        }

        $module->status = $request->status;
        $module->save();

        return Controller::response(200, false, $message = 'Module status updated', $module);
    }

    public function permissionsByModule(int $id): object
    {
        $permissions = Module::find($id)->permissions;
        return Controller::response(200, false, $message = 'Module Permission list', $permissions);
    }

    public function rolesByModule(int $id): object{
        $roles = Module::find($id)->roles;
        return Controller::response(200, false, $message='Module Role list', $roles);
    }
}
