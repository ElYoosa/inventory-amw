<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create("items", function (Blueprint $table) {
      $table->id();
      $table->foreignId("category_id")->constrained()->cascadeOnDelete();
      $table->string("name", 100);
      $table->string("unit", 20)->default("pcs");
      $table->integer("stock")->default(0);
      $table->integer("min_stock")->default(0);
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists("items");
  }
};
