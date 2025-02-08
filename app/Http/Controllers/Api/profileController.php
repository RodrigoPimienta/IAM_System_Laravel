<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleRole;
use App\Models\Profile;
use App\Models\ProfileRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class profileController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: [''])
        ];
    }

    private $columns = [
        'id_profile',
        'name',
        'status',
    ];

    public function index()
    {
        return 'ok';
    }
    /**
 * @OA\Get(
 *     path="/api/profiles",
 *     tags={"Profiles"},
 *     summary="Get list of profiles",
 *     description="Returns a list of all profiles",
 *     @OA\Response(
 *         response=200,
 *         description="Profile list",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Profile list"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id_profile", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Admin"),
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
 *             @OA\Property(property="message", type="string", example="Error fetching profiles"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function all()
    {
        $profiles = Profile::all($this->columns);
        return Controller::response(200, false, $message = 'Profile list', $profiles);
    }
    /**
 * @OA\Post(
 *     path="/api/profiles",
 *     tags={"Profiles"},
 *     summary="Create a new profile",
 *     description="Creates a new profile with associated roles",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Admin"),
 *             @OA\Property(
 *                 property="roles",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id_module", type="integer", example=1),
 *                     @OA\Property(property="id_role", type="integer", example=2)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Profile created"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_profile", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Bad request"),
 *             @OA\Property(property="message", type="string", example="Error creating profile"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function store(Request $request)
    {

        $request = (object) $request->validate([
            'name'              => 'required|max:255',
            'roles'             => 'nullable|array',
            'roles.*.id_module' => 'required|int|exists:modules,id_module',
            'roles.*.id_role'    => 'required|int|exists:modules_roles,id_role',
        ]);

        DB::beginTransaction();

        $profile = Profile::create([
            'name' => $request->name,
        ]);

        if (! $profile) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating profile');
        }

        $arrayRoles = [];

        if (! $request->roles) {
            DB::commit();
            return Controller::response(200, true, $message = 'Profile created', $profile);
        }

        foreach ($request->roles as $role) {
            // add id_profile to the array
            $role = (object) $role;

            // check if the role exists and belongs to the module

            $check = ModuleRole::where(['id_module' => $role->id_module, 'id_role' => $role->id_role])->first();

            if (! $check) {
                DB::rollBack();
                return Controller::response(404, true, $message = 'The role does not exist or does not belong to the module', $role);
            }

            $arrayRoles[] = [
                'id_profile' => $profile->id_profile,
                'id_module'  => $role->id_module,
                'id_role'    => $role->id_role,
                'created_at' => now(),
            ];
        }

        $insertRoles = ProfileRole::insert($arrayRoles);

        if (! $insertRoles) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating profile roles');
        }

        DB::commit();
        return Controller::response(200, true, $message = 'Profile created', $profile);
    }
/**
 * @OA\Get(
 *     path="/api/profiles/{id}",
 *     tags={"Profiles"},
 *     summary="Get a profile by ID",
 *     description="Returns a profile by its ID, along with related roles",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the profile to retrieve",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Profile found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_profile", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="status", type="integer", example=1),
 *                 @OA\Property(
 *                     property="roles",
 *                     type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id_role", type="integer", example=2),
 *                         @OA\Property(property="id_module", type="integer", example=1)
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Profile not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not found"),
 *             @OA\Property(property="message", type="string", example="Profile not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function show(int $id): object
    {

        $profile = Profile::find($id, $this->columns);

        if (! $profile) {
            return Controller::response(404, true, $message = 'Profile not found');
        }

                                 // Cargar los roles relacionados con el perfil
        $profile->load('roles'); // Esto carga los roles relacionados con el perfil

        return Controller::response(200, false, $message = 'Profile found', $profile);
    }

    /**
 * @OA\Put(
 *     path="/api/profiles/{id}",
 *     tags={"Profiles"},
 *     summary="Update a profile",
 *     description="Updates an existing profile with new name and roles",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the profile to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Admin"),
 *             @OA\Property(
 *                 property="roles",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id_module", type="integer", example=1),
 *                     @OA\Property(property="id_role", type="integer", example=2)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Profile updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_profile", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Profile not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not found"),
 *             @OA\Property(property="message", type="string", example="Profile not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function update(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'name'              => 'required|max:255',
            'roles'             => 'nullable|array',
            'roles.*.id_module' => 'required|int|exists:modules,id_module',
            'roles.*.id_rol'    => 'int|exists:modules_roles,id_role',
        ]);


        $profile = Profile::find($id, $this->columns);
        if (! $profile) {
            return Controller::response(404, true, $message = 'Profile not found');
        }

        DB::beginTransaction();

        $profile->name = $request->name;
        $profile->save();

        // update status to 0 for all roles

        $update = ProfileRole::where('id_profile', $profile->id_profile)->update(['status' => 0]);

        if (! $update) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error updating profile roles');
        }

        $arrayRoles = [];

        foreach ($request->roles as $role) {
            // add id_profile to the array
            $role         = (object) $role;

            $check = ModuleRole::where(['id_module' => $role->id_module, 'id_role' => $role->id_role])->first();

            if (! $check) {
                DB::rollBack();
                return Controller::response(404, true, $message = 'The role does not exist or does not belong to the module', $role);
            }
            
            $arrayRoles[] = [
                'id_profile' => $profile->id_profile,
                'id_module'  => $role->id_module,
                'id_role'    => $role->id_role,
                'created_at' => now(),
            ];
        }

        $insertRoles = ProfileRole::insert($arrayRoles);

        if (! $insertRoles) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating profile roles');
        }

        DB::commit();

        return Controller::response(200, false, $message = 'Profile updated', $profile);
    }

    /**
 * @OA\Patch(
 *     path="/api/profiles/{id}/status",
 *     tags={"Profiles"},
 *     summary="Update profile status",
 *     description="Updates the status of a profile",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the profile to update status",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Profile updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_profile", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Profile not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not found"),
 *             @OA\Property(property="message", type="string", example="Profile not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function updateStatus(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'status' => 'required|int|digits:1',
        ]);

        $profile = Profile::find($id, $this->columns);
        if (! $profile) {
            return Controller::response(404, true, $message = 'Profile not found');
        }

        $profile->status = $request->status;

        $profile->save();

        return Controller::response(200, false, $message = 'Profile updated', $profile);
    }

}
