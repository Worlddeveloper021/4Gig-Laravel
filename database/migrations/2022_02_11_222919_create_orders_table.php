<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('package_id')->constrained('packages');
            $table->string('payment_id');
            $table->string('payment_status')->nullable();
            $table->string('duration');
            $table->string('price');
            $table->tinyInteger('status');
            $table->string('channel_name')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
