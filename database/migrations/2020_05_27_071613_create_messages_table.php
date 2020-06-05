<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned();
            $table->text('body')->nullable();
            $table->integer('spamscore')->default(0);
            $table->integer('status')->default(0);
            $table->integer('jabber_id')->nullable();
            $table->integer('conversation_id')->unsigned();
            $table->integer('fack_user_id')->unsigned();
            $table->integer('read')->default(0);
            $table->string('sent_from')->default('api_cruzeiro');
            $table->dateTime('moderated_at')->nullable();
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreign('fack_user_id')->references('id')->on('fack_users')->onDelete('cascade');
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
        Schema::dropIfExists('messages');
    }
}
