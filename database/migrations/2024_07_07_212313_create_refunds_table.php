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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->text('message');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('type_fee_id');
            $table->decimal('amount_spent', 8, 2)->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->date('expense_date');
            $table->text('HR_comment')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('ceiling_reached')->default(false);
            $table->decimal('reimbursement_amount', 8, 2)->nullable();
            $table->boolean('payed')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_fee_id')->references('id')->on('type_fees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
