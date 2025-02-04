<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleRolePermission extends Model
{
    use LocalFactory;

    protected $table = "modules_roles_permissions";

    protected $primaryKey = "id_roles_permission";

    protected $guarded = ["id_roles_permission"];

    protected $fillable = [
        "status",
    ];
}
