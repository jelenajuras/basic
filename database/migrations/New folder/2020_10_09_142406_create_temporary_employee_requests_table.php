<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemporaryEmployeeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temporary_employee_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('type')->comment('vrsta zahtjeva');
			$table->bigInteger('employee_id');
			$table->date('start_date');
			$table->date('end_date');
			$table->time('start_time');
			$table->time('end_time');
			$table->string('comment',500)->nullable();
			$table->string('approve',10)->nullable();
			$table->integer('approved_id')->nullable()->comment('odobrio djelatnik');
			$table->date('approved_date')->nullable()->comment('datum odobrenja');
            $table->string('approve_reason',255)->nullable()->comment('razlog odobrenja');
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
        Schema::dropIfExists('temporary_employee_requests');
    }
}