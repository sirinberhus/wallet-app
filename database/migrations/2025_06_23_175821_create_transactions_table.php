<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->enum('type', ['DEPOSIT', 'WITHDRAWAL', 'PROMOTION', 'ADJUSTMENT']);
            $table->decimal('amount', 15, 2);
            $table->string('reference_id')->unique();
            $table->foreignId('promotion_reward_id')->nullable()->constrained();
            $table->foreignId('processed_by')
            ->nullable()
            ->constrained('backoffice_agents');
            $table->timestamps();

            $table->index('player_id');
            $table->index('reference_id');
            $table->index('player_id', 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
