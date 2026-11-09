<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('lpj_reports', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('submission_id')->unique()->constrained('submissions')->cascadeOnDelete();
            $table->text('report_text')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('validated_by_nidn', 20)->nullable();
            $table->string('validation_status', 20)->nullable()->comment('PENDING, VALID, REVISION');
            $table->text('validation_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpj_reports');
    }
};
