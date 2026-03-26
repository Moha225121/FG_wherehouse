<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('car_models', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., Hyundai Elantra 2020
            $table->text('note')->nullable(); // اضافة ملاحظة لسياره بلكامل
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('car_models'); }
};