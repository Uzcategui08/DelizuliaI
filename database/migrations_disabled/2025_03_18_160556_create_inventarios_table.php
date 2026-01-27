<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('inventarios', function (Blueprint $table) {
      $table->id('id_inventario');
      $table->foreignId('id_producto')->constrained('productos', 'id_producto')->onDelete('cascade');
      $table->foreignId('id_almacen')->constrained('almacenes', 'id_almacen')->onDelete('cascade');
      $table->integer('cantidad');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('inventarios');
  }
};
