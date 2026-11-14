<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('item_claims', function (Blueprint $t) {
            if (!Schema::hasColumn('item_claims', 'reporter_seen_at')) {
                $t->timestamp('reporter_seen_at')->nullable();
            }
            if (!Schema::hasColumn('item_claims', 'rejected_at')) {
                $t->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('item_claims', 'rejection_reason')) {
                $t->string('rejection_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_claims', function (Blueprint $t) {
            $t->dropColumn(['reporter_seen_at', 'rejected_at', 'rejection_reason']);
        });
    }
};