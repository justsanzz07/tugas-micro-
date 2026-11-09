<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('competition_details', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('competition_name', 255);
            $table->string('organizer', 255);
            $table->string('level', 20)->comment('REGIONAL, NASIONAL, INTERNASIONAL');
            $table->string('category', 150);
            $table->string('competition_url', 500)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('location', 255);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_details');
    }
};
