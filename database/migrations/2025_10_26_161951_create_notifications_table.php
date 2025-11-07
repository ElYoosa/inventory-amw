<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create("notifications", function (Blueprint $table) {
      $table->id();
      $table->string("title")->nullable();
      $table->text("message");
      $table->enum("status", ["new", "read"])->default("new");
      $table->string("role_target")->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists("notifications");
  }
};
