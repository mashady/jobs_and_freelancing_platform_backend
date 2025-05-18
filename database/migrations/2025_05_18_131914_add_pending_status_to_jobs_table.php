<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::transaction(function () {
            // 1. Create temporary text column
            Schema::table('jobs', function (Blueprint $table) {
                $table->string('status_temp', 20)->default('open');
            });

            // 2. Copy data from old enum to new text column
            DB::statement('UPDATE jobs SET status_temp = status');

            // 3. Drop the old enum column
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            // 4. Rename the temporary column to original name
            Schema::table('jobs', function (Blueprint $table) {
                $table->renameColumn('status_temp', 'status');
            });

            // 5. Add check constraint for the new status options
            DB::statement("
                ALTER TABLE jobs
                ADD CONSTRAINT jobs_status_check
                CHECK (status IN ('open', 'closed', 'pending'))
            ");
        });
    }

    public function down()
    {
        DB::transaction(function () {
            // 1. Create temporary text column with old options
            Schema::table('jobs', function (Blueprint $table) {
                $table->string('status_temp', 20)->default('open');
            });

            // 2. Copy data from current column to temporary column
            DB::statement('UPDATE jobs SET status_temp = status');

            // 3. Drop the check constraint
            DB::statement('ALTER TABLE jobs DROP CONSTRAINT IF EXISTS jobs_status_check');

            // 4. Drop the current column
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            // 5. Rename the temporary column to original name
            Schema::table('jobs', function (Blueprint $table) {
                $table->renameColumn('status_temp', 'status');
            });

            // 6. Recreate the original check constraint
            DB::statement("
                ALTER TABLE jobs
                ADD CONSTRAINT jobs_status_check
                CHECK (status IN ('open', 'closed'))
            ");
        });
    }
};
