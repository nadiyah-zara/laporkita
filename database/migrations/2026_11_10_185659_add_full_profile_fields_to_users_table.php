<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover_photo')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('disability', 20)->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('website')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'bio', 'avatar', 'cover_photo', 'birth_date',
                'nik', 'gender', 'occupation', 'disability', 'city',
                'address', 'postal_code', 'website'
            ]);
        });
    }
};