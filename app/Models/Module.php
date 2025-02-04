<?php

namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;

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
}
