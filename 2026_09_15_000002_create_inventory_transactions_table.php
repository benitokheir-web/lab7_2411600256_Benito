<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->enum('type',['in','out','adjustment']);
            $table->unsignedInteger('quantity');
            $table->string('reference_document')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['product_id','type']);
            $table->index('created_at');
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_transactions'); }
};
