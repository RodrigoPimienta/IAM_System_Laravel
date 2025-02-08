<?php
namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\ModuleRole;
use App\Models\ModuleRolePermission;
use App\Models\Profile;
use App\Models\ProfileRole;
use App\Models\ProfileUser;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('123456'),
        ]);

        $user = User::where('email', 'test@example.com')->first();

        //1.- Insert modules
        $modules = [
            [
                'name' => 'Users',
                'key'  => 'users',
            ],
            [
                'name' => 'Models',
                'key'  => 'models',
            ],
            [
                'name' => 'Profiles',
                'key'  => 'profiles',
            ],
        ];

        Module::insert($modules);

        //2.- Insert module permissions
        $UserPermissions = [
            [
                'id_module' => 1,
                'name'      => 'Show users',
                'key'       => 'show',
            ],
            [
                'id_module' => 1,
                'name'      => 'Create new user',
                'key'       => 'create',
            ],
            [
                'id_module' => 1,
                'name'      => 'Update user',
                'key'       => 'update',
            ],
            [
                'id_module' => 1,
                'name'      => 'Update user status',
                'key'       => 'updateStatus',
            ],
            [
                'id_module' => 1,
                'name'      => 'Update user password',
                'key'       => 'updatePassword',
            ],
        ];
        ModulePermission::insert($UserPermissions);

        $ModelPermissions = [
            [
                'id_module' => 2,
                'name'      => 'Show models',
                'key'       => 'show',
            ],
            [
                "id_module" => 2,
                "name"      => "Create new module",
                "key"       => "create",
            ],
            [
                "id_module" => 2,
                "name"      => "Update module",
                "key"       => "update",
            ],
            [
                "id_module" => 2,
                "name"      => "Update module status",
                "key"       => "updateStatus",
            ],
            [
                "id_module" => 2,
                "name"      => "Show module permissions",
                "key"       => "showPermissions",
            ],
            [
                "id_module" => 2,
                "name"      => "Add module permission",
                "key"       => "addPermission",
            ],
            [
                "id_module" => 2,
                "name"      => "Update module permission",
                "key"       => "updatePermission",
            ],
            [
                "id_module" => 2,
                "name"      => "Update status module permission",
                "key"       => "updateStatusPermission",
            ],
            [
                "id_module" => 2,
                "name"      => "Show module roles",
                "key"       => "showRoles",
            ],
            [
                "id_module" => 2,
                "name"      => "Add module role",
                "key"       => "addRole",
            ],
            [
                "id_module" => 2,
                "name"      => "Update role permission",
                "key"       => "updatePermission",
            ],
            [
                "id_module" => 2,
                "name"      => "Update status module role",
                "key"       => "updateStatusRole",
            ],
        ];
        ModulePermission::insert($ModelPermissions);

        $ProfilePermissions = [
            [
                'id_module' => 3,
                'name'      => 'Show profiles',
                'key'       => 'show',
            ],
            [
                'id_module' => 3,
                'name'      => 'Create new profile',
                'key'       => 'create',
            ],
            [
                'id_module' => 3,
                'name'      => 'Update profile',
                'key'       => 'update',
            ],
            [
                'id_module' => 3,
                'name'      => 'Update profile status',
                'key'       => 'updateStatus',
            ],
        ];

        ModulePermission::insert($ProfilePermissions);

        //3.- Insert roles
        $modulesRoles = [
            [
                "id_module" => 1,
                "name"      => "Users admin",
            ],
            [
                "id_module"   => 2,
                "name"        => "Modules admin",
            ],
            [
                "id_module"   => 3,
                "name"        => "Profiles admin",
            ],
        ];

        ModuleRole::insert($modulesRoles);

        $rolesPermissions = [
            [
                'id_role'       => 1,
                "id_permission" => 1,
            ],
            [
                'id_role'       => 1,
                "id_permission" => 2,
            ],
            [
                'id_role'       => 1,
                "id_permission" => 3,
            ],
            [
                'id_role'       => 1,
                "id_permission" => 4,
            ],
            [
                'id_role'       => 1,
                "id_permission" => 5,
            ],
        ];

        ModuleRolePermission::insert($rolesPermissions);
        $rolesPermissions = [
            [
                'id_role'       => 2,
                "id_permission" => 6,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 7,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 8,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 9,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 10,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 11,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 12,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 13,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 14,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 15,
            ],
            [
                'id_role'       => 2,
                "id_permission" => 17,
            ]
        ];
        ModuleRolePermission::insert($rolesPermissions);

        $rolesPermissions = [
            [
                'id_role'       => 3,
                "id_permission" => 18,
            ],
            [
                'id_role'       => 3,
                "id_permission" => 19,
            ],
            [
                'id_role'       => 3,
                "id_permission" => 20,
            ],
            [
                'id_role'       => 3,
                "id_permission" => 21,
            ]
        ];
        ModuleRolePermission::insert($rolesPermissions);


        //4.-Insert profile

        Profile::insert([
            "name"  => "System admin",
        ]);

        $profileRoles= [
            [
                'id_profile' => 1,
                "id_module" => 1,
                "id_role"   => 1,
            ],
            [
                'id_profile' => 1,
                "id_module" => 2,
                "id_role"   => 2,
            ],
            [
                'id_profile' => 1,
                "id_module" => 3,
                "id_role"   => 3,
            ],
        ];

        //5.- Insert profile roles
        ProfileRole::insert($profileRoles);

        //6.- Add profile to user

        ProfileUser::insert([
            'id_profile' => 1,
            'id_user'    => $user->id,
        ]);

    }
}
