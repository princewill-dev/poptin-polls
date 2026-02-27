<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropUnique(['poll_id', 'device_uuid']);
            $table->dropColumn('device_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->string('device_uuid')->after('option_id');
            $table->unique(['poll_id', 'device_uuid']);
        });
    }
};
