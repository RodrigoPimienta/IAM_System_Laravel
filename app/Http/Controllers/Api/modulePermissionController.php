<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulePermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class modulePermissionController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: [''])
        ];
    }

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

    /**
 * @OA\Get(
 *     path="/api/modules/permissions",
 *     tags={"Module permissions"},
 *     summary="Get all module permissions",
 *     description="Returns a list of all module permissions",
 *     @OA\Response(
 *         response=200,
 *         description="Module permissions found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module Permission list"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="key", type="string", example="permission_key"),
 *                     @OA\Property(property="name", type="string", example="Permission Name")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
    public function all(): object
    {
        $modulePermissions = ModulePermission::all($this->colums);
        return Controller::response(200, false, $message = 'Module Permission list', $modulePermissions);
    }
/**
 * @OA\Post(
 *     path="/api/modules/permissions",
 *     tags={"Module permissions"},
 *     summary="Create a new module permission",
 *     description="Creates a new permission for a specific module",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="id_module", type="integer", example=1),
 *             @OA\Property(property="key", type="string", example="new_permission_key"),
 *             @OA\Property(property="name", type="string", example="New Permission")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Module Permission created",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module Permission created"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="key", type="string", example="new_permission_key"),
 *                 @OA\Property(property="name", type="string", example="New Permission")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error creating module permission",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error"),
 *             @OA\Property(property="message", type="string", example="Error creating module permission")
 *         )
 *     )
 * )
 */
    public function store(Request $request): object
    {
        $request = (object) $request->validate([
            'id_module' => 'required|int|exists:modules,id_module',
            'key'       => 'required|max:255',
            'name'      => 'required|max:255',
        ]);

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

    /**
 * @OA\Get(
 *     path="/api/modules/permissions/{id}",
 *     tags={"Module permissions"},
 *     summary="Get a specific module permission",
 *     description="Returns the details of a specific module permission",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module permission found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module Permission found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="key", type="string", example="permission_key"),
 *                 @OA\Property(property="name", type="string", example="Permission Name")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module Permission not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="Module Permission not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function show(int $id): object
    {
        $modulePermission = ModulePermission::find($id, $this->colums);

        if (! $modulePermission) {
            return Controller::response(404, true, $message = 'Module Permission not found');
        }

        return Controller::response(200, false, $message = 'Module Permission found', $modulePermission);
    }
    /**
 * @OA\Put(
 *     path="/api/modules/permissions/{id}",
 *     tags={"Module permissions"},
 *     summary="Update a module permission",
 *     description="Updates an existing module permission",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="id_module", type="integer", example=1),
 *             @OA\Property(property="key", type="string", example="updated_permission_key"),
 *             @OA\Property(property="name", type="string", example="Updated Permission")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module Permission updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module Permission updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="key", type="string", example="updated_permission_key"),
 *                 @OA\Property(property="name", type="string", example="Updated Permission")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Module Permission not found or error",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error"),
 *             @OA\Property(property="message", type="string", example="Module Permission not found")
 *         )
 *     )
 * )
 */

    public function update(Request $request, int $id_permission): object
    {
        $request = (object) $request->validate([
            'id_module' => 'required|int|exists:modules,id_module',
            'key'       => 'required|max:255',
            'name'      => 'required|max:255',
        ]);

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
    /**
 * @OA\Patch(
 *     path="/api/modules/permissions/{id}/status",
 *     tags={"Module permissions"},
 *     summary="Update status of a module permission",
 *     description="Updates the status of a specific module permission",
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
 *         description="Module Permission status updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module Permission status updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="key", type="string", example="permission_key"),
 *                 @OA\Property(property="name", type="string", example="Permission Name"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Module Permission not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error"),
 *             @OA\Property(property="message", type="string", example="Module Permission not found")
 *         )
 *     )
 * )
 */

    public function updateStatus(Request $request, int $id): object
    {
        $request = (object) $request->validate([
            'status' => 'required|int|in:0,1',
        ]);

        $modulePermission = ModulePermission::find($id, $this->colums);

        if (! $modulePermission) {
            return Controller::response(400, true, $message = 'Module Permission not found');
        }

        $modulePermission->status = $request->status;
        $modulePermission->save();

        return Controller::response(200, false, $message = 'Module Permission status updated', $modulePermission);
    }

}
