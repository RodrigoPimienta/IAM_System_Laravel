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


}
