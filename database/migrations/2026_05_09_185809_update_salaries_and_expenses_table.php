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
            $table->string('username')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->integer('salary_reset_day')->default(1)->after('remaining_salary');
            $table->string('last_reset_month', 7)->nullable()->after('salary_reset_day'); // format: YYYY-MM
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->unsignedBigInteger('employeeID')->nullable()->change();
            $table->enum('type', ['salary', 'expense'])->default('salary')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            $table->dropColumn('salary_reset_day');
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->unsignedBigInteger('employeeID')->nullable(false)->change();
            $table->dropColumn('type');
        });
    }
};
