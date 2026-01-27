<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('productos', function (Blueprint $table) {
      $table->bigInteger('id_producto')->unsigned()->primary();
      $table->string('item');
      $table->string('marca');
      $table->string('t_llave');
      $table->string('sku');
      $table->decimal('precio');
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('productos');
  }
};
