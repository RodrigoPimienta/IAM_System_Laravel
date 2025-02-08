<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Model
{
    use HasFactory, HasApiTokens;

    protected $table      = "profiles";
    protected $primaryKey = "id_profile";

    protected $guarded  = ["id_profile"];
    protected $fillable = [
        "name",
        "status",
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(ModuleRole::class, 'profiles_roles', 'id_profile', 'id_role')
            ->join('modules', 'modules.id_module', '=', 'modules_roles.id_module') // Unir con módulos
            ->withPivotValue('status', 1)
            ->select([
                'profiles_roles.id_module',
                'modules.name as module_name',
                'modules_roles.id_role',
                'modules_roles.id_module',
                'modules_roles.name as role',
                'profiles_roles.status',
            ]);
    }

    public function permissions(): BelongsToMany
    {
        // un perfil tiene muchos roles y esos roles tienen muchas permisos, por lo tanto un perfil tiene muchos permisos, obten todos los permisos de los roles del perfil

        return $this->belongsToMany(ModulePermission::class, 'profiles_roles', 'id_profile', 'id_role')
            ->join('modules_roles_permissions', 'modules_roles_permissions.id_role', 'profiles_roles.id_role')
            ->join('modules_permissions', 'modules_permissions.id_permission', 'modules_roles_permissions.id_permission')
            ->withPivotValue('status', 1)
            ->select([
                'modules_permissions.id_permission',
                'modules_permissions.id_module',
                'modules_permissions.key',
                'modules_permissions.name',
                'modules_permissions.status',
            ]);
    }

    public function users(): HasMany
    {
        return $this->hasMany(ProfileUser::class, 'id_user', 'id_user');
    }

}
