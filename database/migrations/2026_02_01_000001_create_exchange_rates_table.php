<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('exchange_rates', function (Blueprint $table) {
      $table->id();
      $table->date('date')->unique();
      $table->decimal('rate', 18, 6);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('exchange_rates');
  }
};
