<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('ticket_code')->nullable()->after('id'); // Contoh: #JL2611001
            $table->string('urgency')->default('standar')->after('title'); // standar | kritis
            $table->json('photos')->nullable()->after('photo'); // Untuk multi-foto (minimal 2 foto)
            $table->string('photo_after')->nullable()->after('photos'); // Foto selesai perbaikan
            $table->string('reporter_name')->nullable()->after('photo_after');
            $table->string('reporter_phone')->nullable()->after('reporter_name');
            $table->unsignedTinyInteger('progress_percentage')->default(0)->after('status'); // 0 - 100%
            $table->unsignedTinyInteger('current_step')->default(1)->after('progress_percentage'); // 1 s/d 6 tahap
            $table->string('supervisor_name')->nullable()->after('current_step');
            $table->text('supervisor_notes')->nullable()->after('supervisor_name');
            $table->text('completion_notes')->nullable()->after('supervisor_notes');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_code', 'urgency', 'photos', 'photo_after',
                'reporter_name', 'reporter_phone', 'progress_percentage',
                'current_step', 'supervisor_name', 'supervisor_notes', 'completion_notes'
            ]);
        });
    }
};