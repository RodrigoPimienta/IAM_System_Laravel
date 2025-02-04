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

        DB::statement('CREATE VIEW v_profiles_roles as
            SELECT
                pr.id_module,
                m.name as module,
                m.key as key_module,
                pr.id_role,
                mr.name as role,
                pr.id_profile,
                p.name as profile,
                pr.status
            FROM profiles_roles as pr
            INNER JOIN profiles as p on p.id_profile=pr.id_profile
            INNER JOIN modules_roles as mr on mr.id_role=pr.id_role
            INNER JOIN modules as m on m.id_module=pr.id_module
            WHERE m.id_module=mr.id_module'
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW v_profiles_roles");
    }
};
