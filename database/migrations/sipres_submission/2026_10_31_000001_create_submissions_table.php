<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code', 30)->unique()->comment('Auto-generated: SUB-YYYYMM-XXXXX');
            $table->string('mahasiswa_nim', 20)->index();
            $table->string('prodi_id', 20)->index();
            $table->string('fakultas_id', 20)->index();
            $table->string('dosen_nidn', 20)->index();
            $table->string('current_status', 30)->default('DRAFT')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
