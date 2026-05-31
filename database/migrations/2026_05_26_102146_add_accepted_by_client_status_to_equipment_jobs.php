<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Step 1: Convert to VARCHAR
        DB::statement("ALTER TABLE equipment_jobs MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        
        // Step 2: Clean up any invalid values (set to pending)
        DB::statement("UPDATE equipment_jobs SET status = 'pending' WHERE status NOT IN ('pending', 'accepted', 'completed', 'cancelled')");
        
        // Step 3: Convert back to ENUM with new allowed value
        DB::statement("ALTER TABLE equipment_jobs MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'cancelled', 'accepted_by_client') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE equipment_jobs MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        DB::statement("UPDATE equipment_jobs SET status = 'pending' WHERE status NOT IN ('pending', 'accepted', 'completed', 'cancelled')");
        DB::statement("ALTER TABLE equipment_jobs MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};