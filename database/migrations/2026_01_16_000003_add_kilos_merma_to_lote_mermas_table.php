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
    Schema::table('lote_mermas', function (Blueprint $table) {
      $table->decimal('kilos_merma', 10, 3)->default(0)->after('cantidad_merma');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('lote_mermas', function (Blueprint $table) {
      $table->dropColumn('kilos_merma');
    });
  }
};
