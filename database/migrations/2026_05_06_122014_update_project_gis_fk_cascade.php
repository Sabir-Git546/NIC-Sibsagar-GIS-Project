<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_gis_data', function (Blueprint $table) {

            // 🔥 Drop existing PostgreSQL foreign key constraint
            $table->dropForeign('project_gis_data_projectid_fkey');

            // 🔥 Recreate with CASCADE DELETE
            $table->foreign('projectid')
                ->references('projectid')
                ->on('projects')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('project_gis_data', function (Blueprint $table) {

            // rollback: remove cascade version
            $table->dropForeign('project_gis_data_projectid_fkey');

            // restore normal FK (no cascade)
            $table->foreign('projectid')
                ->references('projectid')
                ->on('projects');
        });
    }
};