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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('asset_symbol');
            $table->decimal('asset_price', 15, 8); // current price at the time
            $table->decimal('amount', 15, 2);
            $table->integer('multiplier')->default(1);
            $table->enum('prediction', ['up', 'down']);
            $table->timestamp('expiration_time');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->enum('result', ['won', 'lost', 'pending'])->default('pending');
            $table->decimal('payout', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
