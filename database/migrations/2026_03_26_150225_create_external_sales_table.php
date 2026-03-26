<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('external_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branchID');
            $table->unsignedBigInteger('employeeID');
            $table->string('sale_type'); // نوع البيع
            $table->decimal('amount', 10, 2); // قيمة البيع
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('external_sales'); }
};