<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function profile(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'profiles_users', 'id_user', 'id_profile')
            ->withPivot('status')
            ->select([
                'profiles.id_profile',
                'profiles.name as profile',
                'profiles.status',
            ]);
    }

    public function permissions()
    {
        return DB::table('profiles_users')
            ->join('profiles_roles', 'profiles_roles.id_profile', '=', 'profiles_users.id_profile')
            ->join('modules_roles_permissions', 'modules_roles_permissions.id_role', '=', 'profiles_roles.id_role')
            ->join('modules_permissions', 'modules_permissions.id_permission', '=', 'modules_roles_permissions.id_permission')
            ->join('modules_roles', 'modules_roles.id_role', '=', 'modules_roles_permissions.id_role')
            ->join('modules', 'modules.id_module', '=', 'modules_permissions.id_module')
            ->join('profiles', 'profiles.id_profile', '=', 'profiles_users.id_profile')
            ->where('profiles_users.id_user', $this->id)
            ->where('profiles_users.status', 1)
            ->where('profiles_roles.status', 1)
            ->where('modules_roles_permissions.status', 1)
            ->where('modules_permissions.status', 1)
            ->where('modules_roles.status', 1)
            ->where('modules.status', 1)
            ->where('profiles.status', 1)
            ->select([
                'modules.name as module',
                'modules.key as module_key',
                'modules_permissions.key',
                'modules_permissions.name as permission',
            ]);
    }
    public function getAccess(): array
    {
        if ($this->permissions->isEmpty()) {
            return [];
        }
    
        return $this->permissions->groupBy('module_key')->mapWithKeys(function ($permissions, $moduleKey) {
            return [
                $moduleKey => [
                    'name' => $permissions->first()->module,
                    'permissions' => $permissions->pluck('permission', 'key')->toArray()
                ]
            ];
        })->toArray(); // Convertimos el resultado en un array puro
    }
    
    

}
