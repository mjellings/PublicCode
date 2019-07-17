<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code')->unsigned();
            $table->string('title', 255);
            $table->string('public_title', 255);
            $table->string('logo')->nullable();
            $table->string('public_logo')->nullable();
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
        Schema::dropIfExists('ticket_statuses');
    }
}
