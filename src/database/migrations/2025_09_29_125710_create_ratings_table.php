<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // 紐づく取引
            $table->foreignId('rater_id')->constrained('users')->onDelete('cascade'); // 評価する側
            $table->foreignId('rated_id')->constrained('users')->onDelete('cascade'); // 評価される側
            $table->tinyInteger('score'); // 1〜5
            $table->unique(['order_id', 'rater_id', 'rated_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
