<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branchID');
            $table->unsignedBigInteger('employeeID');
            $table->unsignedBigInteger('itemID');
            
            $table->integer('quantity')->default(1);
            $table->decimal('system_price', 10, 2); // The retail_price at the time of sale
            $table->decimal('sold_price', 10, 2); // The price the employee confirmed
            
            $table->decimal('discount', 10, 2)->default(0); // If sold < system
            $table->decimal('overprice', 10, 2)->default(0); // If sold > system
            
            $table->text('note')->nullable(); // ملاحظة البيع
            
            // For admin undo feature
            $table->enum('status', ['completed', 'refunded'])->default('completed'); 
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sales'); }
};