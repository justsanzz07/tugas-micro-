<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('achievement_certificates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('achievement_rank', 50)->nullable();
            $table->string('certificate_file_path', 500);
            $table->decimal('skpi_points', 5, 2)->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->string('validated_by_nidn', 20)->nullable();
            $table->string('validation_status', 20)->default('PENDING')->comment('PENDING, VALID, REJECTED');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_certificates');
    }
};
