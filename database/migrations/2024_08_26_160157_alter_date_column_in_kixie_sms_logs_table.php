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
        Schema::table('kixie_sms_logs_csv', function (Blueprint $table) {
            // Change the 'date' column to 'datetime'
            $table->dateTime('date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kixie_sms_logs_csv', function (Blueprint $table) {
            //Change the 'date' column back to 'date' if needed
            $table->date('date')->change();
        });
    }
};
