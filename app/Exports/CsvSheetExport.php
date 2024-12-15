<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class CsvSheetExport implements FromCollection, WithTitle
{
    protected $csvFile;
    protected $sheetName;

    public function __construct($csvFile, $sheetName)
    {
        $this->csvFile = $csvFile;
        $this->sheetName = $sheetName;
    }

    public function collection()
    {
        // Read CSV and return as collection
        return collect(array_map('str_getcsv', file($this->csvFile)));
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}
