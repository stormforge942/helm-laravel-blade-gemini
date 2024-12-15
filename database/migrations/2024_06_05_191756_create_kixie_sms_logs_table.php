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
        Schema::create('kixie_sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('messageid');
            $table->string('from')->nullable();
            $table->string('customernumber')->nullable();
            $table->string('to')->nullable();
            $table->string('businessnumber')->nullable();
            $table->string('direction')->nullable();
            $table->text('message')->nullable();
            $table->integer('businessid')->nullable();
            $table->integer('userid')->nullable();
            $table->string('deal_stage')->nullable();
            $table->string('deal_title')->nullable();
            $table->decimal('deal_value', 10, 2)->nullable();
            $table->string('deal_status')->nullable();
            $table->string('contact_firstName')->nullable();
            $table->string('contact_lastName')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_link')->nullable();
            $table->integer('contact_id')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('contact_success')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_usecase')->nullable();
            $table->boolean('device_isactive')->nullable();
            $table->string('device_pushid')->nullable();
            $table->string('crmlink')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kixie_sms_logs');
    }
};
