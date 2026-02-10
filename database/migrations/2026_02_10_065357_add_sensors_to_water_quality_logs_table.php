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
        Schema::table('water_quality_logs', function (Blueprint $table) {
            $table->float('turbidity')->nullable()->after('tds');
            $table->float('ec')->nullable()->after('turbidity');
            $table->float('do')->nullable()->after('ec');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_quality_logs', function (Blueprint $table) {
            //
        });
    }
};
