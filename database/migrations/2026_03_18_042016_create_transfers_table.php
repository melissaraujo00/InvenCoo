<?php

use App\Models\Movement;
use App\Models\Office;
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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Office::class, 'originating_branch');
            $table->foreignIdFor(Office::class, 'destination_branch');
            $table->foreignIdFor(User::class, 'requesting_user');
            $table->foreignIdFor(User::class, 'user_authorizes');
            $table->dateTime('creation_date');
            $table->dateTime('shipping_date')->nullable();
            $table->dateTime('receipt_date')->nullable();
            $table->enum('status', ['pending', 'preparing', 'shipped', 'in_transit', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->foreignIdFor(Movement::class, 'out_movement_id')->nullable()->constrained('movements');
            $table->foreignIdFor(Movement::class, 'in_movement_id')->nullable()->constrained('movements');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
