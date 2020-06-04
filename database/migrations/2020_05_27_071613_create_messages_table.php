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
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->integer('sender')->unsigned();
            $table->integer('id_conversation')->unsigned();
            $table->foreign('sender')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_conversation')->references('id')->on('conversations')->onDelete('cascade');
            $table->integer('destination');
            $table->timestamp('published_at')->nullable();
            $table->dateTime('read_at')->nullable();
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
