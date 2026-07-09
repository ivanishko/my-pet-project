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
        Schema::table('tournament_seasons', function (Blueprint $table) {
            // Добавляем поле description
            $table->text('description')->nullable()->after('name');

            // Добавляем поле status (upcoming, active, completed)
            $table->string('status')->default('upcoming')->after('end_date');

            // Добавляем поле year для быстрого поиска
            $table->year('year')->nullable()->after('status');

            // Переименовываем is_current в более понятное поле,
            // но оставляем его для обратной совместимости
            // Можно оставить как есть или переименовать
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tournament_seasons', function (Blueprint $table) {
            $table->dropColumn(['description', 'status', 'year']);

            // Если хотите вернуть is_current обратно (он уже есть)
            // $table->boolean('is_current')->default(false)->change();
        });
    }
};
