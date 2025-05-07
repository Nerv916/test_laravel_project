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
        Schema::create('approveds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_id')->constrained('preorders')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('preorders')->onDelete('set null');
            $table->string('no_spo')->unique();
            $table->string('name_cust');
            $table->string('no_surat')->nullable();
            $table->dateTime('order_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approveds');
    }
};
