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
    Schema::create('lotes', function (Blueprint $table) {
      $table->id();
      $table->string('nombre');
      $table->date('fecha_inicio')->nullable();
      $table->timestamps();
    });

    Schema::create('lote_productos', function (Blueprint $table) {
      $table->id();
      $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
      $table->foreignId('id_producto')->constrained('productos', 'id_producto')->cascadeOnDelete();
      $table->integer('cantidad_inicial');
      $table->timestamps();

      $table->unique(['lote_id', 'id_producto']);
    });

    Schema::create('lote_dias', function (Blueprint $table) {
      $table->id();
      $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
      $table->unsignedSmallInteger('dia_numero');
      $table->timestamps();

      $table->unique(['lote_id', 'dia_numero']);
    });

    Schema::create('lote_mermas', function (Blueprint $table) {
      $table->id();
      $table->foreignId('lote_dia_id')->constrained('lote_dias')->cascadeOnDelete();
      $table->foreignId('id_producto')->constrained('productos', 'id_producto')->cascadeOnDelete();
      $table->integer('cantidad_merma')->default(0);
      $table->timestamps();

      $table->unique(['lote_dia_id', 'id_producto']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('lote_mermas');
    Schema::dropIfExists('lote_dias');
    Schema::dropIfExists('lote_productos');
    Schema::dropIfExists('lotes');
  }
};
