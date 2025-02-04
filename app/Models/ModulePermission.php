<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use LocalFactory;

    protected $table = "modules_permissions";
    protected $primaryKey = "id_permission";

    protected $guarded = ["id_permission"];

    protected $fillable = [
        "name",
        "key",
        "status",
    ];
}
