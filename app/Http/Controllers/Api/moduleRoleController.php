<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Validator;
use App\Models\ModuleRole;
use App\Models\ModuleRolePermission;

class moduleRoleController extends Controller
{
    private $colums = [
        'id_role',
        'id_module',
        'name',
        'status',
    ];

    public function index(){
        return 'ok';
    }

    public function all(): object{
        $roles = ModuleRole::all();
        return Controller::response(200, false, $message='Module Role list', $roles);
    }

    public function store(Request $request): object{

        $validator = Validator::make($request->all(),[
            'id_module' => 'required|int|exists:modules,id_module',
            'name'=> 'required',
            'permissions' => 'nullable|array',
            'permissions.*.id_permission' => 'required|int|exists:modules_permissions,id_permission',
        ]);

        if ($validator->fails()){
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }
        DB::beginTransaction();

        $moduleRole = ModuleRole::create([
            'id_module' => $request->id_module,
            'name'=> $request->name,
        ]);

        if(! $moduleRole){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating module role');
        }

        $arrayPermissions = [];

        foreach($request->permissions as $permission){
            $permission = (object) $permission;
            $arrayPermissions[] = [
                'id_role' => $moduleRole->id_role,
                'id_permission' => $permission->id_permission,
                'created_at' => now(),
            ];
        }

        $insertPermissions = ModuleRolePermission::insert($arrayPermissions);

        if(! $insertPermissions){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating module role permissions');
        }

        DB::commit();
        return Controller::response(200, true, $message='Module Role created', $moduleRole);

    }


    public function show (int $id): object{
        $moduleRole = ModuleRole::find($id, $this->colums);

        if(! $moduleRole){
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        $moduleRole->load('permissions');

        return Controller::response(200, false, $message='Module Role found', $moduleRole);
    }

    public function update(Request $request, int $id): object{
        $validator = Validator::make($request->all(),[
            'id_module' => 'required|int|exists:modules,id_module',
            'name'=> 'required',
            'permissions' => 'nullable|array',
            'permissions.*.id_permission' => 'required|int|exists:modules_permissions,id_permission',
        ]);

        if ($validator->fails()){
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }

        DB::beginTransaction();
        $moduleRole = ModuleRole::find($id, $this->colums);

        if(! $moduleRole){
            DB::rollBack();
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        if($moduleRole->id_module != $request->id_module){
            DB::rollBack();
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        $moduleRole->name = $request->name;
        $moduleRole->save();


        $update = ModuleRolePermission::where( 'id_role', $moduleRole->id_role)->update(['status' => 0]);

        if(!$update){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error updating module role permissions');
        }


        $arrayPermissions = [];

        foreach($request->permissions as $permission){
            $permission = (object) $permission;
            $arrayPermissions[] = [
                'id_role' => $moduleRole->id_role,
                'id_permission' => $permission->id_permission,
                'created_at' => now(),
            ];
        }

        $insertPermissions = ModuleRolePermission::insert($arrayPermissions);

        if(! $insertPermissions){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating module role permissions');
        }

        DB::commit();

        return Controller::response(200, true, $message= 'Module Role updated', $moduleRole);
    }

}
