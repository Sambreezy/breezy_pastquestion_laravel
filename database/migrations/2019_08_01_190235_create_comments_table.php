<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('comments', function (Blueprint $table) {
            // identification
            $table->uuid('id')->primary();

            // comments
            $table->string('comment')->nullable();
            $table->string('reply')->nullable();
            $table->uuid('parent_comment_id')->nullable();
            $table->string('user_picture')->nullable();

            // relations
            $table->uuid('past_question_id');
            $table->foreign('past_question_id')->references('id')->on('past_questions');

            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            // status
            $table->integer('flags');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
