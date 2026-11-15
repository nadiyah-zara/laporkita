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
    Schema::table('users', function (Blueprint $t) {
        $t->timestamp('blocked_at')->nullable();
        $t->string('blocked_reason', 255)->nullable();
    });
}

public function down(): void
{
    Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['blocked_at', 'blocked_reason']));
}
};
