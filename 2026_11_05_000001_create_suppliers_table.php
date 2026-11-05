<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('suppliers', function(Blueprint $table) {
   $table->id(); $table->string('company_name')->index(); $table->string('contact_person');
   $table->string('email')->unique(); $table->string('phone'); $table->text('address');
   $table->string('payment_terms')->default('COD'); $table->unsignedInteger('lead_time_days')->default(1);
   $table->decimal('minimum_order_amount',12,2)->default(0); $table->boolean('is_active')->default(true)->index();
   $table->timestamps(); $table->softDeletes();
  });
 }
 public function down(): void { Schema::dropIfExists('suppliers'); }
};
