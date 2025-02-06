<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use LocalFactory;

    protected $table = "modules";
    protected $primaryKey = "id_module";

    protected $guarded = ["id_module"];

    protected $fillable = [
        "name",
        "key",
        "status",
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(ModulePermission::class,"id_module","id_module")->select(['id_permission','name','key','status']);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(ModuleRole::class, 'id_module', 'id_module')->select(['id_role','name','status']);
    }
}
