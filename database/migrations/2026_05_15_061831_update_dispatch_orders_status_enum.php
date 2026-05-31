<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL, modify ENUM column
        DB::statement("ALTER TABLE dispatch_orders MODIFY COLUMN status ENUM('pending', 'assigned', 'accepted', 'delivered') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE dispatch_orders MODIFY COLUMN status ENUM('pending', 'assigned', 'delivered') NOT NULL DEFAULT 'pending'");
    }
};