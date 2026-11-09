<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_finance';

    public function up(): void
    {
        Schema::create('budget_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('submission_id')->index()->comment('Cross-DB ref to sipres_submission.submissions');
            $table->decimal('total_requested', 15, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->string('status', 20)->default('DRAFT')->comment('DRAFT, SUBMITTED, APPROVED, REVISED, REJECTED');
            $table->timestamps();

            $table->unique('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_requests');
    }
};
