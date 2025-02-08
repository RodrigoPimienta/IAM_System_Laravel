<?php
namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class ModuleRole extends Model
{
    use LocalFactory, HasApiTokens;

    protected $table = "modules_roles";

    protected $primaryKey = "id_role";

    protected $guarded  = ["id_role"];
    protected $fillable = [
        'id_module',
        "name",
        "status",
    ];

    public function module(): BelongsTo
    {
        return $this->BelongsTo(Module::class, 'id_module', 'id_module');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            ModulePermission::class,
            'modules_roles_permissions',
            'id_role',
            'id_permission'
        )
            ->withPivot('status')
            ->wherePivot('status', 1)
            ->select([
                'modules_permissions.id_permission',
                'modules_permissions.key as permission_key',
                'modules_permissions.name as permission',
                'modules_roles_permissions.status',
            ]);
    }

    public function profile (): BelongsToMany
    {
        return $this->belongsToMany(
            Profile::class,
            'profiles_roles',
            'id_role',
            'id_profile'
        )
            ->withPivot('status')
            ->wherePivot('status', 1)
            ->select([
                'profiles_roles.id_profile',
                'profiles_roles.status',
            ]);
    }
}
