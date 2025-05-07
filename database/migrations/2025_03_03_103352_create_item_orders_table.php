<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preorder_id')->constrained('preorders')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade'); // Relasi ke produk
            $table->integer('qty');
            $table->decimal('pagu', 15, 2)->default(0);
            $table->decimal('total_pagu', 15, 2)->default(0);
            $table->decimal('harga', 15, 2); // Harga tetap ada, karena harga bisa berbeda di tiap transaksi
            $table->decimal('total_harga', 15, 2);
            $table->decimal('ppn', 5, 2)->default(11);
            $table->decimal('margin', 5, 2)->default(0.7);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });


        // Tambahkan constraint menggunakan raw SQL
        DB::statement('ALTER TABLE item_orders ADD CONSTRAINT qty_check CHECK (qty > 0)');
        DB::statement('ALTER TABLE item_orders ADD CONSTRAINT harga_check CHECK (harga >= 0)');
        DB::statement('ALTER TABLE item_orders ADD CONSTRAINT total_harga_check CHECK (total_harga >= 0)');
        DB::statement('ALTER TABLE item_orders ADD CONSTRAINT margin_check CHECK (margin >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_orders');
    }
};
