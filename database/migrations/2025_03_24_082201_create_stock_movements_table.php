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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_id')->constrained('preorders')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('approved_id')->constrained('approveds')->onDelete('cascade');
            $table->foreignId('approved_item_id')->nullable()->constrained('approved_items')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('preorders')->onDelete('set null');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('ppn', 5, 2)->nullable();
            $table->string('no_batch')->nullable();
            $table->date('exp_date')->nullable();
            $table->decimal('total_harga_jual', 15, 2);
            $table->decimal('total_harga_beli', 15, 2);
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
