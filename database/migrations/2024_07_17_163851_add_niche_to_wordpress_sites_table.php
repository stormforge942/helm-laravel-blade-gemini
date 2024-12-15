<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddNicheToWordpressSitesTable extends Migration
{
    public function up()
    {
        Schema::table('wordpress_sites', function (Blueprint $table) {
            $table->string('niche')->nullable()->after('wp_prefix');
        });
    }

    public function down()
    {
        Schema::table('wordpress_sites', function (Blueprint $table) {
            $table->dropColumn('niche');
        });
    }
}