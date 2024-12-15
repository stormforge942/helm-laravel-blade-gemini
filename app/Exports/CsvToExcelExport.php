<?php
namespace App\Exports;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CsvToExcelExport implements WithMultipleSheets
{
    protected $csvFiles;

    public function __construct($csvFiles)
    {
        $this->csvFiles = $csvFiles;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->csvFiles as $csvFile) {
            // Get the base name without extension
            $fileNameWithoutExtension = pathinfo($csvFile, PATHINFO_FILENAME);
            
            // Add a sheet for each CSV file
            $sheets[] = new CsvSheetExport($csvFile, $fileNameWithoutExtension);
        }

        return $sheets;
    }
}

