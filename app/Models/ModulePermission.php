<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ModulePermission extends Model
{
    use LocalFactory;

    protected $table = "modules_permissions";
    protected $primaryKey = "id_permission";

    protected $guarded = ["id_permission"];

    protected $fillable = [
        "id_module",
        "name",
        "key",
        "status",
    ];

    public function module(): BelongsTo{
        return $this->BelongsTo(Module::class, 'id_module', 'id_module');
    }

}
