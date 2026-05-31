<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE invoices MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE invoices MODIFY status ENUM('pending','paid','overdue') NOT NULL DEFAULT 'pending'");
    }
};