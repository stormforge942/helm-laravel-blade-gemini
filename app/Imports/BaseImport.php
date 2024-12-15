<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\Importable;

abstract class BaseImport implements WithMultipleSheets, SkipsUnknownSheets
{
    use Importable;

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->sheetMapping() as $index => $sheetName) {
            $sheets[$index] = $this->createPerSheetImport($sheetName);
        }
        return $sheets;
    }

    public function onUnknownSheet($sheetName)
    {
        info("Sheet '{$sheetName}' was skipped because it is unknown.");
    }

    abstract protected function sheetMapping(): array;
    abstract protected function createPerSheetImport($sheetName): PerSheetImport;
}
