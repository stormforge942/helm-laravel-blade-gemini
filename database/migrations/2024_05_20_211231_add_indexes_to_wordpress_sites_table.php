<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddIndexesToWordpressSitesTable extends Migration
{
    public function up()
    {
        Schema::table('wordpress_sites', function (Blueprint $table) {
            DB::statement('CREATE INDEX site_url_index ON wordpress_sites (site_url(255))');
            DB::statement('CREATE INDEX server_index ON wordpress_sites (server(100))');
            DB::statement('CREATE INDEX site_url_server_index ON wordpress_sites (site_url(255), server(100))');
        });
    }

    public function down()
    {
        Schema::table('wordpress_sites', function (Blueprint $table) {
            DB::statement('DROP INDEX site_url_index ON wordpress_sites');
            DB::statement('DROP INDEX server_index ON wordpress_sites');
            DB::statement('DROP INDEX site_url_server_index ON wordpress_sites');
        });
    }
}