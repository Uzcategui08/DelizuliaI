<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('price_list_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('price_list_id');
      $table->unsignedBigInteger('id_producto');
      $table->decimal('price_per_kg', 18, 4)->default(0);
      $table->boolean('has_iva')->default(false);

      $table->unique(['price_list_id', 'id_producto']);
      $table->index(['id_producto']);

      $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');
      $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('price_list_items');
  }
};
