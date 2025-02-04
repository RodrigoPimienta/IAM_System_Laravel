<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE VIEW v_module_roles_permissions AS
            SELECT 
                mr.id_module,
                mrp.id_role,
                mr.name as role,
                mrp.id_permission,
                mp.key as key_permission,
                mp.name as permission,
                mrp.status
            from modules_roles_permissions as mrp
            INNER JOIN modules_roles as mr on mr.id_role=mrp.id_role
            INNER JOIN modules_permissions as mp on mp.id_permission=mrp.id_permission
            where mr.id_module=mp.id_module
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW v_module_roles_permissions");
    }
};
