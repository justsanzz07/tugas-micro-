<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_finance';

    public function up(): void
    {
        Schema::create('incentive_awards', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('submission_id')->index()->comment('Cross-DB ref to sipres_submission.submissions');
            $table->string('achievement_rank', 50);
            $table->decimal('incentive_amount', 15, 2);
            $table->string('decided_by_nidn', 20);
            $table->timestamp('decided_at');
            $table->foreignUlid('disbursement_ticket_id')->nullable()->constrained('disbursement_tickets')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_awards');
    }
};
