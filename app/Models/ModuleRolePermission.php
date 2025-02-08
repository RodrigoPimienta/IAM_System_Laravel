<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ModuleRolePermission extends Model
{
    use LocalFactory, HasApiTokens;

    protected $table = "modules_roles_permissions";

    protected $primaryKey = "id_roles_permission";

    protected $guarded = ["id_roles_permission"];

    protected $fillable = [
        "status",
    ];
}
