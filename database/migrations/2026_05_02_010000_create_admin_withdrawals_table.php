<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('admin_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adminID');
            $table->decimal('amount', 12, 2);
            $table->text('note')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamps();

            $table->foreign('adminID')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_withdrawals');
    }
};
