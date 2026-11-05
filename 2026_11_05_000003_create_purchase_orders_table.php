<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('purchase_orders', function(Blueprint $table) {
   $table->id(); $table->string('reference_number')->unique();
   $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
   $table->string('status')->default('draft')->index(); $table->date('order_date');
   $table->date('expected_delivery_date')->nullable(); $table->decimal('subtotal',12,2)->default(0);
   $table->decimal('tax',12,2)->default(0); $table->decimal('total',12,2)->default(0);
   $table->text('notes')->nullable(); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
   $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
   $table->timestamp('approved_at')->nullable(); $table->timestamp('received_at')->nullable();
   $table->text('rejection_reason')->nullable(); $table->timestamps(); $table->softDeletes();
  });
 }
 public function down(): void { Schema::dropIfExists('purchase_orders'); }
};
