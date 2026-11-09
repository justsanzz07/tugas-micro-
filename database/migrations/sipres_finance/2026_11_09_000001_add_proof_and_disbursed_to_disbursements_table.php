<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'sipres_finance';

    public function up(): void
    {
        Schema::table('disbursements', function (Blueprint $table) {
            if (! Schema::hasColumn('disbursements', 'proof_path')) {
                $table->string('proof_path', 255)->nullable()->after('notes');
            }
            if (! Schema::hasColumn('disbursements', 'disbursed_by_user_id')) {
                $table->unsignedBigInteger('disbursed_by_user_id')->nullable()->after('processed_by_name');
            }
            if (! Schema::hasColumn('disbursements', 'disbursed_by_name')) {
                $table->string('disbursed_by_name', 100)->nullable()->after('disbursed_by_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('disbursements', function (Blueprint $table) {
            if (Schema::hasColumn('disbursements', 'proof_path')) {
                $table->dropColumn('proof_path');
            }
            if (Schema::hasColumn('disbursements', 'disbursed_by_user_id')) {
                $table->dropColumn('disbursed_by_user_id');
            }
            if (Schema::hasColumn('disbursements', 'disbursed_by_name')) {
                $table->dropColumn('disbursed_by_name');
            }
        });
    }
};
