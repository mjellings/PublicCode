<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->unsigned()->index();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->dateTime('stat_start');
            $table->integer('stat_mins')->unsigned();
            $table->integer('assigned_to')->nullable()->unsigned()->index();
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->integer('ticket_status_id')->unsigned()->index();
            $table->foreign('ticket_status_id')->references('id')->on('ticket_statuses');
            $table->integer('ticket_category_id')->unsigned()->index();
            $table->foreign('ticket_category_id')->references('id')->on('ticket_categories');
            $table->integer('ticket_impact_id')->unsigned()->index();
            $table->foreign('ticket_impact_id')->references('id')->on('ticket_impacts');
            $table->integer('ticket_priority_id')->unsigned()->index();
            $table->foreign('ticket_priority_id')->references('id')->on('ticket_priorities');
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
        Schema::dropIfExists('tickets');
    }
}
