<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branchID');
            $table->unsignedBigInteger('carModelID');
            $table->unsignedBigInteger('glassPositionID');
            
            // type can be up to 4 string values (e.g., Original, Commercial, Type A, etc.)
            $table->string('glass_type')->nullable(); 
            
            $table->string('shelf_number')->nullable(); // abc - 123
            
            $table->decimal('wholesale_price', 10, 2)->default(0); // Admin only
            $table->decimal('retail_price', 10, 2)->default(0); // Employee price
            
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('items'); }
};