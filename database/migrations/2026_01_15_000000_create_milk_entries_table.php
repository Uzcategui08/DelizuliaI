<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
  {
    Schema::create('milk_entries', function (Blueprint $table) {
      $table->id();
      $table->date('date');
      $table->unsignedBigInteger('payee_id')->nullable();
      $table->string('payee_name')->nullable();
      $table->decimal('liters', 8, 2);
      $table->decimal('amount', 12, 2)->nullable();
      $table->date('week_end')->nullable();
      $table->timestamp('closed_at')->nullable();
      $table->timestamps();

      $table->foreign('payee_id')->references('id')->on('payees')->onDelete('set null');
    });
  }

  public function down()
  {
    Schema::dropIfExists('milk_entries');
  }
};
