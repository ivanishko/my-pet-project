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
		Schema::create('tournament_seasons', function (Blueprint $table) {
		    $table->id();
		    $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
		    $table->string('name'); // Автоматически генерируемое название "2023" или "2023-2024"
		    $table->date('start_date');
		    $table->date('end_date');
		    $table->boolean('is_current')->default(false);
		    $table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_seasons');
    }
};
