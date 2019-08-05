<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('images', function (Blueprint $table) {
            // identification
            $table->uuid('id')->primary();

            // images
            $table->string('name');
            $table->string('url');

            // relations
            $table->uuid('past_question_id');
            $table->foreign('past_question_id')->references('id')->on('past_questions');

            // status
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
        Schema::dropIfExists('images');
    }
}
