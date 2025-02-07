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
        Schema::create('profiles_users', function (Blueprint $table) {
            $table->id('id_profile_user');
            $table->unsignedBigInteger( 'id_profile');
            $table->unsignedBigInteger( 'id_user');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->foreign('id_profile')->references('id_profile')->on('profiles');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles_users');
    }
};
