<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $strings = ['owner_unique_feature', 'owner_inner_color', 'owner_proof_photo', 'claimant_phone'];
    private array $times = ['owner_submitted_at', 'owner_verified_at', 'finder_verified_at', 'claimant_seen_at'];

    public function up(): void
    {
        Schema::table('item_claims', function (Blueprint $t) {
            foreach ($this->strings as $col) {
                if (!Schema::hasColumn('item_claims', $col)) $t->string($col)->nullable();
            }
            if (!Schema::hasColumn('item_claims', 'owner_full_detail')) $t->text('owner_full_detail')->nullable();
            foreach ($this->times as $col) {
                if (!Schema::hasColumn('item_claims', $col)) $t->timestamp($col)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('item_claims', fn (Blueprint $t) => $t->dropColumn(
            array_merge($this->strings, $this->times, ['owner_full_detail'])
        ));
    }
};