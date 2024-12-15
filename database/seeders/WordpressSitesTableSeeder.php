<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Imports\SitesImport;
use App\Models\WpApiDetail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Site;
use App\Exports\CsvToExcelExport;
class WordpressSitesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFiles = [
            storage_path('app/wp_details_csv/homelander.csv'),
            storage_path('app/wp_details_csv/johnwick.csv'),
            storage_path('app/wp_details_csv/jonsnow.csv'),
            storage_path('app/wp_details_csv/mrbean.csv'),
            storage_path('app/wp_details_csv/onepiece.csv'),
            storage_path('app/wp_details_csv/pikachu.csv'),
            storage_path('app/wp_details_csv/saitama.csv'),
            storage_path('app/wp_details_csv/thor.csv'),
            storage_path('app/wp_details_csv/thanos.csv'),
            storage_path('app/wp_details_csv/saphire.csv'),
            storage_path('app/wp_details_csv/spiderman.csv'),
            storage_path('app/wp_details_csv/hulk.csv'),
            storage_path('app/wp_details_csv/gather.csv'),
        ];

        // Generate the Excel file
        Excel::store(new CsvToExcelExport($csvFiles), 'wp_details.xlsx', 'local');

        $this->command->info('CSV files have been converted to Excel.');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Delete related records in wp_api_details
        DB::table('wp_api_details')->delete();

        // Truncate the Site table
        Site::truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Import data from the Excel file
        Excel::import(new SitesImport, storage_path('app/wp_details.xlsx'));
    }
}
