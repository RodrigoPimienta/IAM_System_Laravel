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
        return $this->hasMany(ProfileRole::class, 'id_profile', 'id_profile')
                    ->select(['id_module', 'id_role', 'status'])
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
