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
        Schema::table('reports', function (Blueprint $t) {
            $t->timestamp('hidden_at')->nullable();
            $t->string('hidden_reason', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reports', fn(Blueprint $t) => $t->dropColumn(['hidden_at', 'hidden_reason']));
    }
};
