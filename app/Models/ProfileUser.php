<?php
namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class ProfileUser extends Model
{
    use LocalFactory, HasApiTokens;

    protected $table      = "profiles_users";
    protected $primaryKey = "id_profile_user";

    protected $guarded = ["id_profile_user"];

    protected $fillable = [
        'id_profile',
        'id_user',
        'status',
        'created_at',
    ];

}
