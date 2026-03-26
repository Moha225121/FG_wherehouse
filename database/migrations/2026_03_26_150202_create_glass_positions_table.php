<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('glass_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // امامي, مثلثات اماميه, etc.
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('glass_positions'); }
};