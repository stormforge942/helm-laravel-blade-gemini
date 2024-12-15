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
        Schema::create('kixie_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('callid');
            $table->integer('businessid')->nullable();
            $table->timestamp('calldate')->nullable();
            $table->string('fromnumber')->nullable();
            $table->string('tonumber')->nullable();
            $table->integer('duration')->nullable();
            $table->decimal('amount', 8, 4)->nullable();
            $table->string('externalid')->nullable();
            $table->string('calltype')->nullable();
            $table->string('callstatus')->nullable();
            $table->string('recordingurl')->nullable();
            $table->string('recordingsid')->nullable();
            $table->string('tonumber164')->nullable();
            $table->string('fromnumber164')->nullable();
            $table->string('disposition')->nullable();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('calleridName')->nullable();
            $table->string('email')->nullable();
            $table->string('destinationName')->nullable();
            $table->string('cadenceactionprocessid')->nullable();
            $table->string('powerlistid')->nullable();
            $table->string('HScalltype')->nullable();
            $table->string('powerlistsessionid')->nullable();
            $table->integer('extensionDial')->nullable();
            $table->string('toExt')->nullable();
            $table->string('fromExt')->nullable();
            $table->timestamp('answerDate')->nullable();
            $table->timestamp('callEndDate')->nullable();
            $table->string('externalcrmid')->nullable();
            $table->string('crmlink')->nullable();
            $table->integer('contactid')->nullable();
            $table->string('dealid')->nullable();
            $table->string('webhookurl')->nullable();
            $table->string('outcome')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kixie_call_logs');
    }
};
