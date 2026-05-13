<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('buys', function (Blueprint $table) {
            // factura (iva inc), credito_fiscal (iva exc), exento
            $table->string('document_type')->default('factura')->after('supplier_id');
            // global, item
            $table->string('discount_type')->default('global')->after('discount');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            // Descuento específico por esta línea de producto
            $table->decimal('discount', 12, 2)->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buys', function (Blueprint $table) {
            //
        });
    }
};
