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
        Schema::table('seats', function (Blueprint $table) {
            $table->index('expiry_date');
        });

        Schema::table('aircraft', function (Blueprint $table) {
            $table->index('airline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropIndex(['expiry_date']);
        });

        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropIndex(['airline_id']);
        });
    }
};
