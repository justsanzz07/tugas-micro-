<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_workflow';

    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('approval_flow_id')->constrained('approval_flows')->cascadeOnDelete();
            $table->unsignedTinyInteger('step_order');
            $table->string('approver_role', 10)->comment('DOSEN, DEKAN, WAREK');
            $table->string('approver_identifier', 20)->nullable()->comment('NIDN or structural ID');
            $table->string('status', 20)->default('PENDING')->comment('PENDING, APPROVED, REVISED, REJECTED');
            $table->timestamp('decided_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['approval_flow_id', 'step_order']);
            $table->index('approver_role');
            $table->index('approver_identifier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
