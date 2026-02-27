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
        Schema::table('polls', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('id');
        });

        // Ensure existing polls get a slug
        foreach (\App\Models\Poll::all() as $poll) {
            $slug = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
            while (\App\Models\Poll::where('slug', $slug)->exists()) {
                $slug = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
            }
            $poll->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
