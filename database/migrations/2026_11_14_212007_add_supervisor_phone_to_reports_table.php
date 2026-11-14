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
            $t->string('supervisor_phone', 25)->nullable()->after('supervisor_name');
        });
    }

    public function down(): void
    {
        Schema::table('reports', fn(Blueprint $t) => $t->dropColumn('supervisor_phone'));
    }
};
