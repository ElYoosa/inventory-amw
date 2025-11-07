<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create("out_transactions", function (Blueprint $table) {
      $table->id();
      $table->foreignId("item_id")->constrained("items")->cascadeOnDelete();
      $table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
      $table->date("date");
      $table->integer("qty");
      $table->string("receiver", 100)->nullable();
      $table->string("kode_grup", 50)->nullable();
      $table->text("note")->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists("out_transactions");
  }
};
