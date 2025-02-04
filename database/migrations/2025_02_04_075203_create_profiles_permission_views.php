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
        Schema::create('profiles_permission_views', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        DB::statement('CREATE VIEW v_profiles_permissions as
        select 
            p.id_profile,
            p.name as profile,
            m.id_module,
            m.name as module,
            m.key as key_module, 
            mrp.id_role,
            mr.name as role,
            mrp.id_permission,   
            mp.key as key_permission,
            mp.name as permission,
            mrp.status
        from modules_roles_permissions as mrp
        INNER JOIN modules_roles as mr on mr.id_role=mrp.id_role
        INNER JOIN modules_permissions as mp on mp.id_permission=mrp.id_permission
        INNER JOIN profiles_roles as pr on pr.id_role=mrp.id_role and pr.id_module=mr.id_module
        INNER JOIN modules as m on m.id_module=mr.id_module and mp.id_module=m.id_module
        INNER JOIN profiles as p on p.id_profile=pr.id_profile and p.status=1
        where mr.id_module=mp.id_module and mp.status=1 and mr.status=1 and mrp.status=1 and pr.status=1 and m.status=1
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW v_profiles_permissions");
    }
};
