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
        Schema::table('tournaments', function (Blueprint $table) {
            // Добавляем поле type после location
            $table->string('type')->default('individual')->after('location');

            // Добавляем поле status после type
            $table->string('status')->default('active')->after('type');

            // Добавляем поле image (если его нет)
            if (!Schema::hasColumn('tournaments', 'image')) {
                $table->string('image')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropColumn(['type', 'status']);

            if (Schema::hasColumn('tournaments', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
