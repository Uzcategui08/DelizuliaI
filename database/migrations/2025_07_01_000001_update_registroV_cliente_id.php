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
    Schema::table('registroV', function (Blueprint $table) {
      // Eliminar el campo string cliente si existe
      if (Schema::hasColumn('registroV', 'cliente')) {
        $table->dropColumn('cliente');
      }
      // Agregar el campo id_cliente y la foreign key
      $table->unsignedBigInteger('id_cliente')->nullable();
      $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('registroV', function (Blueprint $table) {
      $table->dropForeign(['id_cliente']);
      $table->dropColumn('id_cliente');
      $table->string('cliente')->nullable();
    });
  }
};
