<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('adminID')->nullable()->after('employeeID');
            $table->unsignedBigInteger('employeeID')->nullable()->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('adminID')->nullable()->after('employeeID');
            $table->unsignedBigInteger('employeeID')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('adminID');
            $table->unsignedBigInteger('employeeID')->nullable(false)->change();
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('adminID');
            $table->unsignedBigInteger('employeeID')->nullable(false)->change();
        });
    }
};
