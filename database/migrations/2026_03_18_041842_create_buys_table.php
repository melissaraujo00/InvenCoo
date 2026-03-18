<?php

use App\Models\Office;
use App\Models\Supplier;
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
        Schema::create('buys', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 12, 2);
            $table->date('date');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->nullable();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Office::class);
            $table->decimal('total_iva', 12,2);
            $table->foreignIdFor(Supplier::class);
            $table->tinyInteger('is_cancelled', false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buys');
    }
};
