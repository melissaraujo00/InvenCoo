<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Office;
use App\Models\Unit;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 50);
            $table->foreignIdFor(Category::class)->constrained();
            $table->foreignIdFor(Brand::class)->nullable()->constrained();
            $table->foreignIdFor(Unit::class)->constrained();
            $table->integer('stock_minimun');
            $table->integer('stock');
            $table->foreignIdFor(Office::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
