<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')
            ->constrained('promotions', 'id')
            ->cascadeOnDelete();
            $table->enum('type', ['CASH', 'BONUS_SPIN', 'FREE_BET', 'OTHER']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            $table->index(['promotion_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_rewards');
    }
}
