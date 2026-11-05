<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('products', function(Blueprint $table) {
   $table->foreignId('supplier_id')->nullable()->after('supplier')->constrained('suppliers')->nullOnDelete();
   $table->decimal('average_daily_usage',10,2)->default(0);
   $table->unsignedInteger('safety_stock')->default(0);
   $table->unsignedInteger('minimum_order_quantity')->default(1);
   $table->unsignedInteger('package_size')->default(1);
  });
 }
 public function down(): void {
  Schema::table('products', function(Blueprint $table) {
   $table->dropConstrainedForeignId('supplier_id');
   $table->dropColumn(['average_daily_usage','safety_stock','minimum_order_quantity','package_size']);
  });
 }
};
