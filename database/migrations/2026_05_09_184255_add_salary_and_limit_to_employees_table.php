<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('salary', 12, 2)->default(0)->after('status');
            $table->decimal('daily_withdrawal_limit', 10, 2)->default(0)->after('salary');
            $table->decimal('remaining_salary', 12, 2)->default(0)->after('daily_withdrawal_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['salary', 'daily_withdrawal_limit', 'remaining_salary']);
        });
    }
};
