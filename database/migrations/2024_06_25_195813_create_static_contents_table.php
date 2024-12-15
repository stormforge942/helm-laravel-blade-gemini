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
        Schema::create('niche_static_contents', function (Blueprint $table) {
            $table->id();
            $table->string('niche');
            $table->text('services_content');
            $table->text('choose_us_content');
            $table->text('contact_us_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niche_static_contents');
    }
};
