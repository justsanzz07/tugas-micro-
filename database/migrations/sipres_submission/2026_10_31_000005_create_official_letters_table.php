<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('official_letters', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('letter_type', 20)->comment('DISPENSASI, SURAT_TUGAS');
            $table->string('letter_number', 100)->unique();
            $table->string('issued_by_role', 10)->comment('DEKAN, WAREK');
            $table->timestamp('issued_at');
            $table->string('file_path', 500);
            $table->string('qr_code_token', 100)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_letters');
    }
};
