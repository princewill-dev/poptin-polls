<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_id')->constrained()->cascadeOnDelete();
            $table->string('device_uuid');
            $table->timestamps();

            $table->unique(['poll_id', 'device_uuid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
