<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('price_invoices', function (Blueprint $table) {
      $table->id();
      $table->date('fecha');
      $table->unsignedBigInteger('id_cliente')->nullable();
      $table->unsignedBigInteger('price_list_id');
      $table->decimal('tasa', 18, 6);
      $table->decimal('iva_rate', 5, 4)->default(0.16);
      $table->json('items');
      $table->decimal('base_total', 18, 2)->default(0);
      $table->decimal('iva_total', 18, 2)->default(0);
      $table->decimal('total', 18, 2)->default(0);

      $table->foreign('price_list_id')->references('id')->on('price_lists');
      $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('price_invoices');
  }
};
