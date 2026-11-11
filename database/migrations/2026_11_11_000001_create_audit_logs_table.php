<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->uuid('correlation_id')->nullable();
                $table->string('service_name', 50)->default('sipres');
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('user_role', 50)->nullable()->index();
                $table->string('action', 50)->index();
                $table->string('resource_type', 50)->nullable()->index();
                $table->string('resource_id', 50)->nullable()->index();
                $table->json('payload_before')->nullable();
                $table->json('payload_after')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
