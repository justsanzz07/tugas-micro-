<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_finance';

    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('budget_request_id')->constrained('budget_requests')->cascadeOnDelete();
            $table->string('item_name', 255);
            $table->unsignedInteger('quantity')->default(1);
            $table->string('unit', 50)->default('unit');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
