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
            $table->string('ip_address')->nullable()->after('device_uuid');
            $table->unique(['poll_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropUnique(['poll_id', 'ip_address']);
            $table->dropColumn('ip_address');
        });
    }
};
