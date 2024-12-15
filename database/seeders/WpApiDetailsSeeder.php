<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WpApiDetail;
use App\Imports\WpApiDetailsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class WpApiDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WpApiDetail::truncate();
        Excel::import(new WpApiDetailsImport, storage_path('app/wp_api_details.xlsx'));
    }
}
