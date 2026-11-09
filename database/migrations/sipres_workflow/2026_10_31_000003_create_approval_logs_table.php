<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_workflow';

    public function up(): void
    {
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('approval_step_id')->constrained('approval_steps')->cascadeOnDelete();
            $table->string('action', 20)->comment('SUBMIT, APPROVE, REVISE, REJECT, RESUBMIT');
            $table->string('actor_identifier', 20)->index();
            $table->string('actor_role', 15)->comment('MAHASISWA, DOSEN, DEKAN, WAREK');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('IP, user-agent, extra context');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
