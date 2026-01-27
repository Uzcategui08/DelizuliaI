<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('camiones', function (Blueprint $table) {
      $table->id();
      $table->string('nombre');
      $table->string('placa')->nullable();
      $table->unsignedBigInteger('ultimo_cambio_aceite_km')->nullable();
      $table->boolean('activo')->default(true);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('camiones');
  }
};
