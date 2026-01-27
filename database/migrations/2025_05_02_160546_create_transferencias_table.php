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
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id('id_transferencia');
            $table->foreignId('id_producto')->constrained('productos', 'id_producto');
            $table->foreignId('id_almacen_origen')->constrained('almacenes', 'id_almacen');
            $table->foreignId('id_almacen_destino')->constrained('almacenes', 'id_almacen');
            $table->integer('cantidad');
            $table->foreignId('user_id')->constrained('users');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencias');
    }
};
