<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('simple_sales', function (Blueprint $table) {
      $table->id();
      $table->date('fecha_h');
      $table->unsignedBigInteger('id_cliente')->nullable();
      $table->string('zona')->nullable();
      $table->json('items');
      $table->decimal('total_bruto', 12, 2)->default(0);
      $table->decimal('descuento', 12, 2)->default(0);
      $table->decimal('total_neto', 12, 2)->default(0);
      $table->unsignedBigInteger('id_empleado')->nullable();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('simple_sales');
  }
};
