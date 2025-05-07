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
        Schema::create('approved_items', function (Blueprint $table) {
            $table->id();
        
            $table->foreignId('approved_id')->constrained('approveds')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
        
            $table->integer('qty');
            $table->string('satuan')->nullable(); // tambahan
            $table->string('merek')->nullable();  // tambahan
        
            $table->decimal('pagu', 15, 2)->nullable(); // tambahan
            $table->decimal('ppn', 5, 2)->nullable();   // tambahan
            $table->decimal('harga', 15, 2);            // existing
            $table->decimal('harga_setelah_pajak', 15, 2)->nullable(); // tambahan
            $table->decimal('total_harga_beli', 15, 2)->nullable();     // tambahan
            $table->decimal('margin', 5, 2)->nullable();                // tambahan
            $table->decimal('harga_jual_satuan', 15, 2);                // existing
            $table->decimal('selisih_pagu', 15, 2)->nullable();         // tambahan
        
            $table->decimal('total_harga', 15, 2); // existing
        
            $table->string('status')->default('approved'); // existing
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved_items');
    }
};
