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
        Schema::table('federations', function (Blueprint $table) {
            // Добавляем новые поля
            $table->string('logo')->nullable()->after('description');
            $table->string('website')->nullable()->after('logo');
            $table->string('email')->nullable()->after('website');
            $table->string('phone')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('federations', function (Blueprint $table) {
            // Удаляем добавленные поля
            $table->dropColumn(['logo', 'website', 'email', 'phone']);
        });
    }
};
