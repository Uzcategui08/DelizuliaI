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
        Schema::create('ajuste_inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_producto')->constrained('productos', 'id_producto')->onDelete('cascade');
            $table->foreignId('id_almacen')->constrained('almacenes', 'id_almacen')->onDelete('cascade');
            $table->enum('tipo_ajuste', ['compra', 'resta', 'ajuste', 'ajuste2']); // Tipos específicos
            $table->integer('cantidad_anterior');
            $table->integer('cantidad_nueva');
            $table->integer('diferencia')->storedAs('cantidad_nueva - cantidad_anterior'); // Campo calculado
            $table->text('descripcion')->nullable(); // Ej: "Compra a proveedor X", "Daño en almacén"
            $table->foreignId('user_id')->constrained('users'); // Responsable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuste_inventarios');
    }
};
