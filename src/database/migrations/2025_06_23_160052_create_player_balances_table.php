<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_balances', function (Blueprint $table) {
            $table->foreignId('player_id')
            ->constrained('players', 'id')
            ->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();

            $table->primary('player_id'); //one to one relationship
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_balances');
    }
}
