<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Server;

class ServersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Server::truncate();

        $servers = [
            ['name' => 'testing', 'ip_address' => '172.234.196.157'],
            ['name' => 'goku', 'ip_address' => '172.233.219.195'],
            ['name' => 'homelander', 'ip_address' => '172.233.219.31'],
            ['name' => 'johnwick', 'ip_address' => '172.233.223.187'],
            ['name' => 'jonsnow', 'ip_address' => '172.233.223.86'],
            ['name' => 'mrbean', 'ip_address' => '172.234.196.138'],
            ['name' => 'obsidian', 'ip_address' => '172.233.223.249'],
            ['name' => 'onepiece', 'ip_address' => '172.234.196.144'],
            ['name' => 'pikachu', 'ip_address' => '172.234.221.6'],
            ['name' => 'saitama', 'ip_address' => '172.234.221.7'],
            ['name' => 'thor', 'ip_address' => '172.234.31.114'],
            ['name' => 'thanos', 'ip_address' => '172.234.221.78'],
            ['name' => 'saphire', 'ip_address' => '172.234.19.93'],
            ['name' => 'gather', 'ip_address' => '172.232.20.220'],
            ['name' => 'spiderman', 'ip_address' => '172.234.219.59'],
            ['name' => 'hulk', 'ip_address' => '172.236.98.216'],
        ];

        foreach ($servers as $server) {
            Server::create($server);
        }
    }
}
