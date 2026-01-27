<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('lote_productos', function (Blueprint $table) {
      if (!Schema::hasColumn('lote_productos', 'kilos_por_unidad')) {
        $table->decimal('kilos_por_unidad', 10, 3)->default(1)->after('cantidad_inicial');
      }
    });
  }

  public function down(): void
  {
    Schema::table('lote_productos', function (Blueprint $table) {
      if (Schema::hasColumn('lote_productos', 'kilos_por_unidad')) {
        $table->dropColumn('kilos_por_unidad');
      }
    });
  }
};
