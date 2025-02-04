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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id('id_profile');
            $table->string('name',255);
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->id('id_module');
            $table->string('name',255);
            $table->integer('status')->default(1);
            $table->timestamps();
        });

        Schema::create('modules_permissions', function (Blueprint $table) {
            $table->id('id_permission');
            $table->unsignedBigInteger( 'id_module');
            $table->string('key',50);
            $table->string('name',50);
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->foreign('id_module')->references('id_module')->on('modules');
        });

        Schema::create('modules_roles', function (Blueprint $table) {
            $table->id('id_role');
            $table->unsignedBigInteger( 'id_module');
            $table->string('name',50);
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->foreign('id_module')->references('id_module')->on('modules');
        });

        Schema::create('modules_roles_permissions', function (Blueprint $table) {
            $table->id('id_role_permission');
            $table->unsignedBigInteger( 'id_role');
            $table->unsignedBigInteger( 'id_permission');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->foreign('id_role')->references('id_role')->on('modules_roles');
            $table->foreign('id_permission')->references('id_permission')->on('modules_permissions');
        });

        Schema::create('profiles_roles', function (Blueprint $table) {
            $table->id('id_profile_role');
            $table->unsignedBigInteger( 'id_profile');
            $table->unsignedBigInteger( 'id_module');
            $table->unsignedBigInteger( 'id_role');
            $table->timestamps();
            $table->foreign('id_profile')->references('id_profile')->on('profiles');
            $table->foreign('id_module')->references('id_module')->on('modules');
            $table->foreign('id_role')->references('id_role')->on('modules_roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('modules_permissions');
        Schema::dropIfExists('modules_roles');
        Schema::dropIfExists('modules_roles_permissions');
        Schema::dropIfExists('profiles_roles');
    }
};
