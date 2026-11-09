<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('lpj_attachments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('lpj_report_id')->constrained('lpj_reports')->cascadeOnDelete();
            $table->string('attachment_type', 20)->comment('NOTA, BUKTI_KEGIATAN, LAINNYA');
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpj_attachments');
    }
};
