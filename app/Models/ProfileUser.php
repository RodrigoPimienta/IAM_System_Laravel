<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileUser extends Model
{
    protected $fillable = [
        'id_profile',
        'id_user',
        'status',
    ];

}
