<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'item_kind')) {
                $table->string('item_kind')->nullable()->after('type'); // 'kehilangan' atau 'penemuan'
            }
            $table->date('event_date')->nullable()->after('address'); // Tanggal kejadian
            $table->string('event_time')->nullable()->after('event_date'); // Perkiraan jam kejadian
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['item_kind', 'event_date', 'event_time']);
        });
    }
};