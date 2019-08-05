<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePastQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('past_questions', function (Blueprint $table) {
            // identification
            $table->uuid('id')->primary();

            // past questions
            $table->string('department');
            $table->string('course_name');
            $table->string('course_code')->nullable();
            $table->string('semester')->nullable();
            $table->year('year');
            $table->json('tags')->nullable();

            // relations
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');            

            // status
            $table->string('uploaded_by');
            $table->bigInteger('vote_up')->default(0);
            $table->bigInteger('vote_down')->default(0);
            $table->boolean('approved')->default(false);
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
        Schema::dropIfExists('past_questions');
    }
}
