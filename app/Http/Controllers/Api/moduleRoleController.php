<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulePermission;
use App\Models\ModuleRole;
use App\Models\ModuleRolePermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class moduleRoleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['']),
        ];
    }
    private $colums = [
        'id_role',
        'id_module',
        'name',
        'status',
    ];

    public function index()
    {
        return 'ok';
    }

    /**
 * @OA\Get(
 *     path="/modules/roles",
 *     tags={"Module roles"},
 *     summary="Get list of module roles",
 *     description="Returns a list of all module roles",
 *     @OA\Response(
 *         response=200,
 *         description="Module Role list",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="false"),
 *             @OA\Property(property="message", type="string", example="Module Role list"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id_role", type="integer", example=1),
 *                     @OA\Property(property="id_module", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Admin Role"),
 *                     @OA\Property(property="status", type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="true"),
 *             @OA\Property(property="message", type="string", example="Error fetching module roles"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function all(): object
    {
        $roles = ModuleRole::all();
        return Controller::response(200, false, $message = 'Module Role list', $roles);
    }

    /**
 * @OA\Post(
 *     path="/modules/roles",
 *     tags={"Module roles"},
 *     summary="Create a new module role",
 *     description="Creates a new module role and its associated permissions",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="id_module", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin Role"),
 *                 @OA\Property(
 *                     property="permissions",
 *                     type="array",
 *                     items=@OA\Items(
 *                         @OA\Property(property="id_permission", type="integer", example=1)
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Module Role created",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="false"),
 *             @OA\Property(property="message", type="string", example="Module Role created"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_role", type="integer", example=1),
 *                 @OA\Property(property="id_module", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin Role"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="true"),
 *             @OA\Property(property="message", type="string", example="Error creating module role"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function store(Request $request): object
    {

        $request = (object) $request->validate([
            'id_module'                   => 'required|int|exists:modules,id_module',
            'name'                        => 'required',
            'permissions'                 => 'nullable|array',
            'permissions.*.id_permission' => 'required|int|exists:modules_permissions,id_permission',
        ]);

        DB::beginTransaction();

        $moduleRole = ModuleRole::create([
            'id_module' => $request->id_module,
            'name'      => $request->name,
        ]);

        if (! $moduleRole) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating module role');
        }

        $arrayPermissions = [];

        foreach ($request->permissions as $permission) {
            $permission = (object) $permission;

            $permisionCheck = ModulePermission::find($permission->id_permission);

            if (! $permisionCheck) {
                DB::rollBack();
                return Controller::response(404, true, $message = 'Permission not found', $permission);
            }

            if ($permisionCheck->id_module != $request->id_module) {
                DB::rollBack();
                return Controller::response(404, true, $message = 'Permission not foun in module', $permission);
            }

            $arrayPermissions[] = [
                'id_role'       => $moduleRole->id_role,
                'id_permission' => $permission->id_permission,
                'created_at'    => now(),
            ];
        }

        $insertPermissions = ModuleRolePermission::insert($arrayPermissions);

        if (! $insertPermissions) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating module role permissions');
        }

        DB::commit();
        return Controller::response(200, true, $message = 'Module Role created', $moduleRole);

    }

    /**
 * @OA\Get(
 *     path="/modules/roles/{id}",
 *     tags={"Module roles"},
 *     summary="Get module role by ID",
 *     description="Returns the module role by its ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the module role",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module Role found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="false"),
 *             @OA\Property(property="message", type="string", example="Module Role found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_role", type="integer", example=1),
 *                 @OA\Property(property="id_module", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin Role"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module Role not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="true"),
 *             @OA\Property(property="message", type="string", example="Module Role not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function show(int $id): object
    {
        $moduleRole = ModuleRole::find($id, $this->colums);

        if (! $moduleRole) {
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        $moduleRole->load('permissions');

        return Controller::response(200, false, $message = 'Module Role found', $moduleRole);
    }

    /**
 * @OA\Put(
 *     path="/modules/roles/{id}",
 *     tags={"Module roles"},
 *     summary="Update an existing module role",
 *     description="Updates the module role and its permissions",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the module role",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="id_module", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin Role"),
 *                 @OA\Property(
 *                     property="permissions",
 *                     type="array",
 *                     items=@OA\Items(
 *                         @OA\Property(property="id_permission", type="integer", example=1)
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module Role updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="false"),
 *             @OA\Property(property="message", type="string", example="Module Role updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_role", type="integer", example=1),
 *                 @OA\Property(property="id_module", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin Role"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="true"),
 *             @OA\Property(property="message", type="string", example="Error updating module role"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function update(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'id_module'                   => 'required|int|exists:modules,id_module',
            'name'                        => 'required',
            'permissions'                 => 'nullable|array',
            'permissions.*.id_permission' => 'required|int|exists:modules_permissions,id_permission',
        ]);

        DB::beginTransaction();
        $moduleRole = ModuleRole::find($id, $this->colums);

        if (! $moduleRole) {
            DB::rollBack();
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        if ($moduleRole->id_module != $request->id_module) {
            DB::rollBack();
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        $moduleRole->name = $request->name;
        $moduleRole->save();

        $update = ModuleRolePermission::where('id_role', $moduleRole->id_role)->update(['status' => 0]);

        if (! $update) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error updating module role permissions');
        }

        $arrayPermissions = [];

        foreach ($request->permissions as $permission) {
            $permission = (object) $permission;

            $permisionCheck = ModulePermission::find($permission->id_permission);

            if (! $permisionCheck) {
                DB::rollBack();
                return Controller::response(404, true, $message = 'Permission not found', $permission);
            }

            if ($permisionCheck->id_module != $request->id_module) {
                DB::rollBack();
                return Controller::response(404, true, $message = 'Permission not foun in module', $permission);
            }

            $arrayPermissions[] = [
                'id_role'       => $moduleRole->id_role,
                'id_permission' => $permission->id_permission,
                'created_at'    => now(),
            ];
        }

        $insertPermissions = ModuleRolePermission::insert($arrayPermissions);

        if (! $insertPermissions) {
            DB::rollBack();
            return Controller::response(400, true, $message = 'Error creating module role permissions');
        }

        DB::commit();

        return Controller::response(200, true, $message = 'Module Role updated', $moduleRole);
    }

    /**
 * @OA\Patch(
 *     path="/modules/roles/{id}/status",
 *     tags={"Module roles"},
 *     summary="Update module role status",
 *     description="Updates the status of the module role",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the module role",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             properties={
 *                 @OA\Property(property="status", type="integer", example=1)
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module Role status updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="false"),
 *             @OA\Property(property="message", type="string", example="Module Role status updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id_role", type="integer", example=1),
 *                 @OA\Property(property="id_module", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Admin Role"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="true"),
 *             @OA\Property(property="message", type="string", example="Error updating module role status"),
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

        $role = ModuleRole::find($id, $this->colums);

        if (! $role) {
            return Controller::response(404, true, $message = 'Module Role not found');
        }

        $role->status = $request->status;
        $role->save();

        return Controller::response(200, false, $message = 'Module role status updated', $role);

    }

}
