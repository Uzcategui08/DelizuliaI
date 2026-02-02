<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('price_invoices', function (Blueprint $table) {
      if (Schema::hasColumn('price_invoices', 'id_cliente')) {
        // Intentar eliminar FK si existe; algunos motores lo requieren.
        try {
          $table->dropForeign(['id_cliente']);
        } catch (\Throwable $e) {
          // Ignorar si no existe o el driver no soporta.
        }
        $table->dropColumn('id_cliente');
      }
    });
  }

  public function down(): void
  {
    Schema::table('price_invoices', function (Blueprint $table) {
      if (!Schema::hasColumn('price_invoices', 'id_cliente')) {
        $table->unsignedBigInteger('id_cliente')->nullable()->after('fecha');
        try {
          $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
        } catch (\Throwable $e) {
          // Ignorar si el driver no soporta.
        }
      }
    });
  }
};
