<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained()
                  ->onDelete('cascade'); // 取引削除時に関連チャットも削除
            $table->foreignId('sender_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // ユーザー削除時に関連チャットも削除
            $table->text('message'); // 必須（バリデーションで400文字まで制御）
            $table->string('image')->nullable(); // 任意（添付画像）
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
        Schema::dropIfExists('chats');
    }
}
