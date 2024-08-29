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
        Schema::create('type_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('percentage')->nullable();
            $table->enum('refund_type', ['percentage', 'per_unit']);
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('ceiling', 8, 2)->nullable();
            $table->enum('ceiling_type', ['none', 'per_day', 'per_year'])->default('none');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_fees');
    }
};
