<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWpApiDetailsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter the wordpress_sites table columns to match the required data types
        Schema::table('wordpress_sites', function (Blueprint $table) {
            $table->string('site_url', 255)->change();
            $table->string('server', 100)->change();
        });

        // Create the wp_api_details table
        Schema::create('wp_api_details', function (Blueprint $table) {
            $table->id();
            $table->string('site_url', 255);
            $table->string('server', 100);
            $table->text('username');
            $table->text('password');
            $table->text('application_password');
            $table->timestamps();

            // Add the foreign key constraint
            $table->foreign(['site_url', 'server'])
                  ->references(['site_url', 'server'])
                  ->on('wordpress_sites')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_api_details');
    }
}
