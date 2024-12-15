<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use App\Models\NicheContent;
use Exception;

class NicheStaticContentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NicheContent::truncate();
        $csvFilePath = storage_path('app/niche_static_contents.csv');

        try {
            $csv = Reader::createFromPath($csvFilePath, 'r');
            $csv->setHeaderOffset(0); // Ensures the first row is treated as headers

            // Validate for duplicate column names
            $header = $csv->getHeader();
            if (count($header) !== count(array_unique($header))) {
                throw new Exception('The CSV header contains duplicate column names.');
            }

            // Iterate over each row and insert into the database
            foreach ($csv as $row) {
            // Check for required columns and handle missing data
                DB::table('niche_static_contents')->insert([
                    'niche' => $row['Niche'],
                    'services_content' => $row['Services'],
                    'choose_us_content' => $row['Choose us'],
                    'contact_us_content' => $row['Contact'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (Exception $e) {
            $this->command->error('Error reading CSV file: ' . $e->getMessage());
        } 
    }
}
