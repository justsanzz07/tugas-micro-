<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_submission';

    public function up(): void
    {
        Schema::create('submission_members', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('nim', 20)->index();
            $table->string('role_in_team', 50)->default('Anggota');
            $table->timestamps();

            $table->unique(['submission_id', 'nim']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_members');
    }
};
