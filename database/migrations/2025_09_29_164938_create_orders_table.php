<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->date('order_date');
            $table->date('deadline_date');
            $table->enum('order_status', [
                'On-Process',
                'Designing',
                'Production',
                'For Releasing',
                'Completed',
                'Cancelled'
            ])->default('On-Process');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('layout_design_fee', 10, 2)->default(0);
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('layout_employee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
