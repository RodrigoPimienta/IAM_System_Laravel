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

    /**
 * @OA\Get(
 *     path="/api/modules",
 *     tags={"Modules"},
 *     summary="Get list of modules",
 *     description="Returns a list of all modules",
 *     @OA\Response(
 *         response=200,
 *         description="Module list",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module list"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Module A"),
 *                     @OA\Property(property="key", type="string", example="module_a"),
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
 *             @OA\Property(property="message", type="string", example="Error fetching modules"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function all(): object{
        $modules = Module::all($this->colums);
        return Controller::response(200, false, $message = 'Module list', $modules);
    }

    /**
 * @OA\Post(
 *     path="/api/modules",
 *     tags={"Modules"},
 *     summary="Create a new module",
 *     description="Creates a new module",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Module A"),
 *             @OA\Property(property="key", type="string", example="module_a")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Module created",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module created"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Module A"),
 *                 @OA\Property(property="key", type="string", example="module_a"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Bad request"),
 *             @OA\Property(property="message", type="string", example="Error creating module"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

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
    /**
 * @OA\Get(
 *     path="/api/modules/{id}",
 *     tags={"Modules"},
 *     summary="Get module details",
 *     description="Returns the details of a specific module",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module found"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Module A"),
 *                 @OA\Property(property="key", type="string", example="module_a"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="Module not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


    public function show(int $id): object{
        $module = Module::find($id, $this->colums);
        if(! $module){
            return Controller::response(404, true, $message = 'Module not found');
        }

        return Controller::response(200, false, $message = 'Module found', $module);
    }

    /**
 * @OA\Put(
 *     path="/api/modules/{id}",
 *     tags={"Modules"},
 *     summary="Update module information",
 *     description="Updates the details of a specific module",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Module A Updated"),
 *             @OA\Property(property="key", type="string", example="module_a_updated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Module A Updated"),
 *                 @OA\Property(property="key", type="string", example="module_a_updated"),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="Module not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

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
    /**
 * @OA\Patch(
 *     path="/api/modules/{id}/status",
 *     tags={"Modules"},
 *     summary="Update module status",
 *     description="Updates the status (active/inactive) of a specific module",
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
 *         description="Module status updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module status updated"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="status", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="Module not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


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

    /**
 * @OA\Get(
 *     path="/api/modules/{id}/permissions",
 *     tags={"Modules"},
 *     summary="Get permissions by module",
 *     description="Returns the list of permissions for a specific module",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
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
 *                     @OA\Property(property="name", type="string", example="Permission A"),
 *                     @OA\Property(property="key", type="string", example="permission_a")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="Module not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function permissionsByModule(int $id): object
    {
        $permissions = Module::find($id)->permissions;
        return Controller::response(200, false, $message = 'Module Permission list', $permissions);
    }

    /**
 * @OA\Get(
 *     path="/api/modules/{id}/roles",
 *     tags={"Modules"},
 *     summary="Get roles by module",
 *     description="Returns the list of roles for a specific module",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Module roles found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example=""),
 *             @OA\Property(property="message", type="string", example="Module Role list"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="Role A")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Module not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Not Found"),
 *             @OA\Property(property="message", type="string", example="Module not found"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function rolesByModule(int $id): object{
        $roles = Module::find($id)->roles;
        return Controller::response(200, false, $message='Module Role list', $roles);
    }
}
