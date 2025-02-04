<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleRole extends Model
{
    use LocalFactory;


    protected $table = "modules_roles";

    protected $primaryKey = "id_role";

    protected $guarded = ["id_role"];
    protected $fillable = [
        "name",
        "status",
    ];

    public function permissions(): HasMany{
        return $this->hasMany(ModulePermission::class, 'id_role', 'id_role')
                    ->table('v_module_roles_permissions')
                    ->select(['id_module, id_role, role, id_permission, key_permission, permission, status'])
                    ->where('status', 1);
    }
}