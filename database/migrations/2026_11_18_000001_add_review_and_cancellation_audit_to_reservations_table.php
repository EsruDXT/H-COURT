<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // reviewed_at mungkin sudah ditambahkan oleh migration lokal sebelumnya.
        $addReviewedAt = ! Schema::hasColumn('reservations', 'reviewed_at');
        $addReviewedBy = ! Schema::hasColumn('reservations', 'reviewed_by');
        $addCancelledAt = ! Schema::hasColumn('reservations', 'cancelled_at');
        $addCancelledBy = ! Schema::hasColumn('reservations', 'cancelled_by');

        Schema::table('reservations', function (Blueprint $table) use ($addReviewedAt, $addReviewedBy, $addCancelledAt, $addCancelledBy) {
            if ($addReviewedAt) {
                $table->timestamp('reviewed_at')->nullable()->after('status');
            }
            if ($addReviewedBy) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            }
            if ($addCancelledAt) {
                $table->timestamp('cancelled_at')->nullable()->after('reviewed_by');
            }
            if ($addCancelledBy) {
                $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            if (Schema::hasColumn('reservations', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
            }
            if (Schema::hasColumn('reservations', 'cancelled_by')) {
                $table->dropForeign(['cancelled_by']);
            }
        });

        // reviewed_at sengaja dipertahankan saat rollback karena kolom ini
        // mungkin berasal dari migration lokal yang sudah digunakan sebelumnya.
        $columns = array_values(array_filter(
            ['reviewed_by', 'cancelled_at', 'cancelled_by'],
            fn (string $column): bool => Schema::hasColumn('reservations', $column),
        ));

        if ($columns !== []) {
            Schema::table('reservations', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
