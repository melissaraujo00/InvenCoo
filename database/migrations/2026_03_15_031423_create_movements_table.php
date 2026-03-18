<?php

use App\Models\Office;
use App\Models\Product;
use App\Models\Type;
use App\Models\User;
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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(Office::class)->constrained();
            $table->date('date_movement');
            $table->foreignIdFor(Type::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->integer('transaction_id');
            $table->integer('amount');
            $table->text('description');
            $table->integer('stock_total');
            $table->char('input_type', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
