<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use DB;
use App\Models\Module;


class moduleController extends Controller
{
    //

    private $colimns = [
        'id_module',
        'name',
        'key',
        'status',
    ];

    public function index(){
        return 'ok';
    }

    public function all(): object{
        $modules = Module::all($this->colimns);
        return Controller::response(200, false, $message = 'Module list', $modules);
    }

    public function store(Request $request): object{
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|max:255',
            'key' => 'required|max:255',
        ]);

        if ($validator->fails())
        {
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }

        $module = Module::create([
            'name' => $request->name,
            'key' => $request->key,
        ]);

        if(! $module){
            return Controller::response(400, true, $message = 'Error creating module');
        }

        return Controller::response(200, false, $message = 'Module created', $module);
    }

    public function show(int $id): object{
        $module = Module::find($id, $this->colimns);
        if(! $module){
            return Controller::response(404, true, $message = 'Module not found');
        }

        return Controller::response(200, false, $message = 'Module found', $module);
    }

    public function update(Request $request, int $id): object{
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|max:255',
            'key' => 'required|max:255',
        ]);

        if ($validator->fails())
        {
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }

        $module = Module::find($id);
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
        $validator = Validator::make($request->all(),
        [
            'status'=> ['required','string','max:1'],
        ]);

        if ($validator->fails())
        {
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }

        $module = Module::find($id);
        if(! $module){
            return Controller::response(404, true, $message = 'Module not found');
        }

        $module->status = $request->status;
        $module->save();

        return Controller::response(200, false, $message = 'Module status updated', $module);
    }
}
