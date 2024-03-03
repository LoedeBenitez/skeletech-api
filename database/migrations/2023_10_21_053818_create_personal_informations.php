<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('personal_informations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('prefix')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('alias')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('updated_by_id')->nullable();
            $table->timestamps();

            $table->foreign('updated_by_id')->references('id')->on('personal_informations');

            // Define the foreign key constraint
            $table->foreign('email')->references('email')->on('credentials')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('personal_informations');
    }
};
