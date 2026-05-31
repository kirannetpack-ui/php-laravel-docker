<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users');
            $table->string('pickup_address');
            $table->text('description')->nullable();
            $table->integer('estimated_boxes');
            $table->string('contact_person');
            $table->string('contact_phone');
            $table->enum('status', ['pending', 'accepted', 'completed'])->default('pending');
            $table->foreignId('assigned_vehicle_id')->nullable()->constrained('vehicles');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pickup_requests');
    }
};