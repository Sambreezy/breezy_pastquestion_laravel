<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // identifications
            $table->uuid('id')->primary();
            $table->string('email')->unique();

            // users
            $table->string('name');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->date('birth_date')->nullable();
            $table->year('birth_year')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('picture', 255)->nullable();
            $table->mediumText('description')->nullable();
            $table->string('provider', 100)->nullable()->default('application');
            $table->string('provider_id', 100)->nullable();

            // status
            $table->boolean('blocked')->default(false);
            $table->bigInteger('votes')->default(0);
            $table->string('rank')->default('user');
            $table->unsignedSmallInteger('flag')->default(0);
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
        Schema::dropIfExists('users');
    }
}
