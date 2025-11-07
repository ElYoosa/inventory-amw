<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE notifications MODIFY notified_at DATETIME NULL');
        } catch (\Throwable $e) {
            // noop: if driver unsupported, leave as is
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE notifications MODIFY notified_at DATE NULL');
        } catch (\Throwable $e) {
            // noop
        }
    }
};

