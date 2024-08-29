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
        Schema::create('refund_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refund_id');
            $table->string('document_path');
            $table->timestamps();

            $table->foreign('refund_id')->references('id')->on('refunds')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_documents');
    }
};
