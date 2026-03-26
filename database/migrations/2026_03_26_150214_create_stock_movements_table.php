<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('itemID');
            $table->unsignedBigInteger('employeeID');
            $table->enum('movement_type', ['add', 'damaged']); // اضافة او تالف
            $table->integer('quantity');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_movements'); }
};