<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Akun yang mengklaim
            $table->string('claim_code')->unique(); // e.g. #BRGT261211001

            // 1. Verifikasi Ciri Spesifik Rahasia
            $table->text('secret_unique_feature'); // Identitas unik barang
            $table->text('secret_inner_color');     // Warna furing / bagian dalam
            $table->text('secret_full_detail');     // Deskripsi rinci kepemilikan

            // 2. Dokumen Bukti
            $table->string('proof_photo')->nullable(); // Foto lama bersama barang

            // 3. Status Alur Stepper (1 s/d 6)
            // Step 2: pending (Belum Diverifikasi)
            // Step 3: verified (Diverifikasi Penemu)
            // Step 4: agreement (Kesepakatan)
            // Step 5: ready_pickup (Tiket QR Pengambilan)
            // Step 6: completed (Selesai)
            $table->unsignedTinyInteger('current_step')->default(2);
            $table->enum('status', ['pending', 'verified', 'agreement', 'ready_pickup', 'completed', 'rejected'])->default('pending');

            // 4. Data Kesepakatan Pengambilan Fisik (Step 4 & 5)
            $table->string('pickup_location')->nullable(); // Contoh: SPBU Sebelah Kopi Kenangan Dr. Mansyur
            $table->date('pickup_date')->nullable();
            $table->string('pickup_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_claims');
    }
};