<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Convert to VARCHAR to allow any status value temporarily
        DB::statement("ALTER TABLE dispatch_orders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        
        // Step 2: Update any existing rows (ensure they have valid values for the new ENUM)
        DB::statement("UPDATE dispatch_orders SET status = 'pending' WHERE status NOT IN ('pending', 'accepted', 'delivered')");
        
        // Step 3: Convert back to ENUM with the new allowed values
        DB::statement("ALTER TABLE dispatch_orders MODIFY COLUMN status ENUM('pending', 'accepted', 'delivered', 'accepted_by_client') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert to the old ENUM without 'accepted_by_client'
        DB::statement("ALTER TABLE dispatch_orders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE dispatch_orders SET status = 'pending' WHERE status NOT IN ('pending', 'accepted', 'delivered')");
        DB::statement("ALTER TABLE dispatch_orders MODIFY COLUMN status ENUM('pending', 'accepted', 'delivered') NOT NULL DEFAULT 'pending'");
    }
};