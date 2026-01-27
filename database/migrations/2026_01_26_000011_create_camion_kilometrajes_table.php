<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('camion_kilometrajes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('camion_id')->constrained('camiones')->cascadeOnDelete();
      $table->date('fecha');
      $table->unsignedBigInteger('kilometraje');
      $table->text('nota')->nullable();
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();

      $table->unique(['camion_id', 'fecha']);
      $table->index(['camion_id', 'fecha']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('camion_kilometrajes');
  }
};
