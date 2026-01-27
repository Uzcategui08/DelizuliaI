<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('productos', function (Blueprint $table) {
      if (!Schema::hasColumn('productos', 'kilos_promedio')) {
        $table->decimal('kilos_promedio', 10, 3)->default(1)->after('precio');
      }
    });
  }

  public function down(): void
  {
    Schema::table('productos', function (Blueprint $table) {
      if (Schema::hasColumn('productos', 'kilos_promedio')) {
        $table->dropColumn('kilos_promedio');
      }
    });
  }
};
