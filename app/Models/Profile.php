<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Profile extends Model
{
    use HasFactory, HasApiTokens;

    protected $table      = "profiles";
    protected $primaryKey = "id_profile";

    protected $guarded  = ["id_profile"];
    protected $fillable = [
        "name",
        "status",
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(ModuleRole::class, 'profiles_roles', 'id_profile', 'id_role')
            ->join('modules', 'modules.id_module', '=', 'modules_roles.id_module') // Unir con módulos
            ->withPivotValue('status', 1)
            ->select([
                'profiles_roles.id_module',
                'modules.name as module_name', 
                'modules_roles.id_role',
                'modules_roles.id_module',
                'modules_roles.name as role',
                'profiles_roles.status',
            ]);
    }
    

}
