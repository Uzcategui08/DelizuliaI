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
        Schema::create('nempleados', function (Blueprint $table) {
            $table->id('id_nempleado');
            $table->foreignId('id_empleado')->constrained('empleados', 'id_empleado')->onDelete('cascade');
            $table->json('id_abonos')->nullable();
            $table->json('id_descuentos')->nullable();
            $table->json('id_costos')->nullable();
            $table->decimal('sueldo_base', 10, 2)->default(0);
            $table->decimal('total_descuentos', 10, 2)->default(0);
            $table->decimal('total_abonos', 10, 2)->default(0);
            $table->decimal('total_prestamos', 10, 2)->default(0);
            $table->decimal('total_costos', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2);
            $table->json('metodo_pago');
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->decimal('horas_trabajadas', 8, 2)->nullable();
            $table->string('tipo_pago_empleado', 20)->nullable();
            $table->text('detalle_pago')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nempleados');
    }
};
