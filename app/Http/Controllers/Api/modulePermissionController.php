<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulePermission;
use Illuminate\Http\Request;
use Validator;

class modulePermissionController extends Controller
{
    private $colums = [
        'id_permission',
        'id_module',
        'key',
        'name',
        'status',
    ];

    public function index()
    {
        return 'ok';
    }

    public function all(): object
    {
        $modulePermissions = ModulePermission::all($this->colums);
        return Controller::response(200, false, $message = 'Module Permission list', $modulePermissions);
    }

    public function store(Request $request): object
    {
        $validator = Validator::make($request->all(),
            [
                'id_module' => 'required|int|exists:modules,id_module',
                'key'       => 'required|max:255',
                'name'      => 'required|max:255',
            ]);

        if ($validator->fails()) {
            return Controller::response(400, false, $message = 'Validation error', $validator->errors());
        }

        $modulePermission = ModulePermission::create([
            'id_module' => $request->id_module,
            'key'       => $request->key,
            'name'      => $request->name,
        ]);

        if (! $modulePermission) {
            return Controller::response(400, true, $message = 'Error creating module permission');
        }

        return Controller::response(201, false, $message = 'Module Permission created', $modulePermission);

    }

    public function show(int $id): object
    {
        $modulePermission = ModulePermission::find($id, $this->colums);

        if (! $modulePermission) {
            return Controller::response(404, true, $message = 'Module Permission not found');
        }

        return Controller::response(200, false, $message = 'Module Permission found', $modulePermission);
    }

    public function update(Request $request, int $id_permission): object
    {
        $validator = Validator::make($request->all(),
            [
                'id_module' => 'required|int|exists:modules,id_module',
                'key'       => 'required|max:255',
                'name'      => 'required|max:255',
            ]);

        if ($validator->fails()) {
            return Controller::response(400, false, $message = 'Validation error', $validator->errors());
        }

        $modulePermission = ModulePermission::find($id_permission, $this->colums);

        if (! $modulePermission) {
            return Controller::response(400, true, $message = 'Module Permission not found');
        }

        if ($modulePermission->id_module != $request->id_module) {
            return Controller::response(400, true, $message = 'Module Permission not found');
        }

        $modulePermission->key  = $request->key;
        $modulePermission->name = $request->name;
        $modulePermission->save();

        return Controller::response(200, false, $message = 'Module Permission updated', $modulePermission);
    }

    public function updateStatus(Request $request, int $id_status): object{
        $validator = Validator::make($request->all(),
        [
            'status' => 'required|int|in:0,1',
        ]);

        if ($validator->fails()) {
            return Controller::response(400, false, $message = 'Validation error', $validator->errors());
        }

        $modulePermission = ModulePermission::find($id_status, $this->colums);

        if (! $modulePermission) {
            return Controller::response(400, true, $message = 'Module Permission not found');
        }

        $modulePermission->status = $request->status;
        $modulePermission->save();

        return Controller::response(200, false, $message = 'Module Permission status updated', $modulePermission);
    }

}
