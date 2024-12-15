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
        Schema::create('kixie_sms_logs_csv', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();;
            $table->string('first_name')->nullable();;
            $table->string('last_name')->nullable();;
            $table->string('type')->nullable();;
            $table->string('internal_sms_id')->nullable();
            $table->string('external_contact')->nullable();
            $table->text('message')->nullable();;
            $table->string('crm_link')->nullable();
            $table->string('status')->nullable();;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kixie_sms_logs_csv');
    }
};
