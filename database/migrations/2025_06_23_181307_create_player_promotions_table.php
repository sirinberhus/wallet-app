<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_promotions', function (Blueprint $table) {
            $table->foreignId('player_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->foreignId('promotion_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->timestamp('claimed_at')->useCurrent();
            $table->timestamps();

            $table->primary(['player_id', 'promotion_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_promotions');
    }
}
