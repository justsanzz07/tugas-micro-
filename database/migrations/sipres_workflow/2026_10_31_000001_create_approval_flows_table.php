<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_workflow';

    public function up(): void
    {
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('submission_id')->index()->comment('Cross-DB ref to sipres_submission.submissions');
            $table->string('current_status', 30)->default('DRAFT')->index();
            $table->unsignedTinyInteger('current_step_order')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_flows');
    }
};
