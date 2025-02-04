<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfileRole extends Model
{
    use LocalFactory;

    protected $table = "profiles_roles";
    protected $primaryKey = "id_profile_role";

    protected $guarded = ["id_profile_role"];
    protected $fillable = [
        "status",
    ];

    public function roles(): HasMany
    {
        return $this->hasMany(ProfileRole::class, 'id_profile', 'id_profile')
                    ->table ('profiles_roles')
                    ->select(['id_module', 'id_role'])
                    ->where('status', 1)
                    ->with([
                        'module' => function ($query) {
                            $query->select('id_module', 'name as module');
                        },
                        'role' => function ($query) {
                            $query->select('id_role', 'name as role');
                        }
                    ]);
    }

}
