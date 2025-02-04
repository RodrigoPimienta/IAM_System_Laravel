<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Validator;
use App\Models\Profile;
use App\Models\ProfileRole;


class profileController extends Controller
{

    private $columns = [
        'id_profile',
        'name',
        'status',
    ]; 

    public function index()
    {
        return 'ok';
    }

    public function all(){
        $profiles = Profile::all($this->columns);
        return Controller::response(200, false, $message = 'Profile list', $profiles);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:255',
            'roles' => 'nullable|array',
            'roles.*.id_module' => 'required|int|exists:modules,id_module',
            'roles.*.id_rol' => 'int|exists:modules_roles,id_role',
        ]);

        if ($validator->fails()){
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }

        DB::beginTransaction();

        $profile = Profile::create([
            'name' => $request->name,
        ]);

        if(! $profile){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating profile');
        }

        $arrayRoles = [];

        foreach($request->roles as $role){
            // add id_profile to the array
            $arrayRoles[] = [
                'id_profile' => $profile->id_profile,
                'id_module' => $role->id_module,
                'id_role' => $role->id_role,
            ];  
        }

        $insertRoles = ProfileRole::insert($arrayRoles);

        if(! $insertRoles){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating profile roles');
        }

        DB::commit();
        return Controller::response(200, true, $message='Profile created', $profile);
    }

    public function show(int $id): object
    {

        $profile = Profile::find($id, $this->columns);

    
        if (! $profile) {
            return Controller::response(404, true, $message = 'Profile not found');
        }
    
        // Cargar los roles relacionados con el perfil
        $profile->load('roles');  // Esto carga los roles relacionados con el perfil
    
        return Controller::response(200, false, $message = 'Profile found', $profile);
    }

    public function update(Request $request, int $id): object
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:255',
            'roles' => 'nullable|array',
            'roles.*.id_module' => 'required|int|exists:modules,id_module',
            'roles.*.id_rol' => 'int|exists:modules_roles,id_role',
        ]);

        if ($validator->fails()){
            return Controller::response(404, false, $message = 'Validation error', $validator->errors());
        }

        $profile = Profile::find($id);
        if(! $profile){
            return Controller::response(404, true, $message = 'Profile not found');
        }

        DB::beginTransaction();

        $profile->name = $request->name;
        $profile->save();

        // update status to 0 for all roles

        $update = ProfileRole::where('id_profile', $profile->id_profile)->update(['status' => 0]);

        if(! $update){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error updating profile roles');
        }

        $arrayRoles = [];

        foreach($request->roles as $role){
            // add id_profile to the array
            $arrayRoles[] = [
                'id_profile' => $profile->id_profile,
                'id_module' => $role->id_module,
                'id_role' => $role->id_role,
            ];  
        }

        $insertRoles = ProfileRole::insert($arrayRoles);

        if(! $insertRoles){
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating profile roles');
        }

        DB::commit();

        return Controller::response(200, false, $message = 'Profile updated', $profile);
    }

    public function updatePartial(Request $request, int $id): object
    {
        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'status' => 'digits:1',
        ]);

        if( $validator->fails() ){
            return Controller::response(400, true, $message = 'Validation error', $validator->errors());
        }

        $profile = Profile::find($id);
        if(! $profile){
            return Controller::response(404, true, $message = 'Profile not found');
        }

        if($request->name){
            $profile->name = $request->name;
        }

        if( $request->status){
            $profile->status = $request->status;
        }

        $profile->save();

        return Controller::response(200, false, $message = 'Profile updated', $profile);
    }

}
