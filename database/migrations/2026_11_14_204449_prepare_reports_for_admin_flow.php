<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $t) {
            // Jaga-jaga kalau status berupa ENUM yang belum mengenal 'rejected'
            $t->string('status', 30)->default('pending')->change();

            if (!Schema::hasColumn('reports', 'scheduled_date')) {
                $t->date('scheduled_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $t) {
            $t->dropColumn('scheduled_date');
        });
    }
};