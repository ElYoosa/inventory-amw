<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tujuan: Menyelaraskan skema tabel notifications dengan logic di TransactionObserver
     * - Menambahkan kolom relasi ke item (item_id)
     * - Menambahkan tanggal notifikasi (notified_at) untuk mencegah duplikasi per hari
     * - Menambahkan kolom user_id (opsional) untuk pelacakan pembuat/target
     * - Menambahkan index untuk query cepat pada (item_id, status) dan kolom terkait
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Relasi ke item (nullable agar aman untuk notifikasi umum)
            $table->foreignId('item_id')->nullable()->after('id')->constrained('items')->cascadeOnDelete();

            // Tanggal notifikasi (dipakai untuk deduplikasi per hari)
            $table->date('notified_at')->nullable()->after('item_id');

            // Opsional: relasi ke user
            $table->foreignId('user_id')->nullable()->after('role_target')->constrained('users')->nullOnDelete();

            // Index yang sering dipakai pada observer/dashboard
            $table->index(['item_id', 'status']);
            $table->index('status');
            $table->index('notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Hapus index terlebih dahulu
            $table->dropIndex(['item_id', 'status']);
            $table->dropIndex(['status']);
            $table->dropIndex(['notified_at']);

            // Hapus constraint dan kolom
            $table->dropForeign(['item_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['item_id', 'notified_at', 'user_id']);
        });
    }
};

