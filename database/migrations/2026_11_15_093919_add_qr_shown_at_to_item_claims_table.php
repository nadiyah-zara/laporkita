<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('item_claims', function (Blueprint $t) {
            $t->timestamp('qr_shown_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('item_claims', fn(Blueprint $t) => $t->dropColumn('qr_shown_at'));
    }
};
