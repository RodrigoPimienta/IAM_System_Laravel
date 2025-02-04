<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    protected $table = "profiles";
    protected $primaryKey = "id_profile";

    protected $guarded = ["id_profile"];
    protected $fillable = [
        "name",
        "status",
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(ModulePermission::class, 'id_profile', 'id_profile')
        ->table('v_profiles_roles')
        ->select(['id_module','module', 'key_module', 'id_role', 'role','status'])
        ->where('status', 1);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(ModulePermission::class, 'id_profile', 'id_profile')
        ->table('v_profiles_permissions')
        ->select(['id_module','module', 'key_module', 'key_permission', 'permission'])
        ->where('status', 1);
    }

    

}
