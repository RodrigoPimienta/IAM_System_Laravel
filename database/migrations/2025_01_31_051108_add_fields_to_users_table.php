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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_last_name')->nullable();
            $table->string('second_last_name')->nullable();
            $table->string('status')->default('active'); // Valor por defecto
            $table->foreignId('id_profile')->nullable()->constrained('profiles')->onDelete('set null'); // Relación con tabla profiles
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_last_name', 'second_last_name', 'status']);
            $table->dropForeign(['id_profile']); // Eliminar la clave foránea
            $table->dropColumn('id_profile');
        });
    }
};
