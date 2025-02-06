<?php
namespace App\Models;

use Carbon\Traits\LocalFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ModuleRole extends Model
{
    use LocalFactory;

    protected $table = "modules_roles";

    protected $primaryKey = "id_role";

    protected $guarded  = ["id_role"];
    protected $fillable = [
        "name",
        "status",
    ];

    public function module(): BelongsTo
    {
        return $this->BelongsTo(Module::class, 'id_module', 'id_module');
    }

}
