<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_finance';

    public function up(): void
    {
        Schema::create('disbursement_tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('funding_decision_id')->unique()->constrained('funding_decisions')->cascadeOnDelete();
            $table->string('ticket_code', 50)->unique()->comment('Auto-generated for SIMPAY');
            $table->decimal('amount', 15, 2);
            $table->string('recipient_nim', 20)->index();
            $table->string('simpay_status', 20)->default('PENDING')->comment('PENDING, PROCESSING, DISBURSED, FAILED');
            $table->string('simpay_reference', 100)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disbursement_tickets');
    }
};
