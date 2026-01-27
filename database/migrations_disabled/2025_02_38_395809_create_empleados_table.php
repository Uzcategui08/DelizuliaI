<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('empleados', function (Blueprint $table) {
      $table->id('id_empleado');
      $table->string('nombre');
      $table->integer('cedula');
      $table->integer('tipo');
      $table->string('cargo');
      $table->enum('tipo_pago', ['sueldo', 'comision', 'horas', 'retiro']);
      $table->decimal('salario_base', 10, 2)->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('empleados');
  }
};
