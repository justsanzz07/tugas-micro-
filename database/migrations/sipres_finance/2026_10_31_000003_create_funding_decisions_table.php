<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_finance';

    public function up(): void
    {
        Schema::create('funding_decisions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('budget_request_id')->unique()->constrained('budget_requests')->cascadeOnDelete();
            $table->decimal('approved_amount', 15, 2);
            $table->string('decided_by_nidn', 20);
            $table->timestamp('decided_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_decisions');
    }
};
