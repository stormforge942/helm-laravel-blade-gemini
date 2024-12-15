<?php

namespace App\Imports;

use App\Models\Site;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\Importable;

interface PerSheetImport extends ToModel, WithHeadingRow, SkipsUnknownSheets {}

class SitesImport extends BaseImport implements WithMultipleSheets, SkipsUnknownSheets
{
    protected function sheetMapping(): array
    {
        return ['goku', 'homelander', 'johnwick', 'jonsnow', 'mrbean', 'obsidian', 'onepiece', 'pikachu', 'saitama', 'thor', 'thanos', 'saphire', 'gather','hulk','spiderman'];
    }

    protected function createPerSheetImport($sheetName): PerSheetImport
    {
        return new PerSheetImportForSites($sheetName);
    }

    public function onUnknownSheet($sheetName)
    {
        // Handle unknown sheet
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->sheetMapping() as $sheetName) {
            $sheets[$sheetName] = $this->createPerSheetImport($sheetName);
        }
        return $sheets;
    }
}

class PerSheetImportForSites implements PerSheetImport
{
    use Importable;

    protected $sheetName;

    public function __construct($sheetName = null)
    {
        $this->sheetName = $sheetName;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row)
    {
        // Ensure all values are treated as text and handle edge cases
        foreach ($row as $key => $value) {
            if (is_string($value) && !empty($value) && $value[0] === '=') {
                $row[$key] = "'" . $value; // Adding a single quote to treat it as text
            } elseif (!isset($value)) {
                $row[$key] = ''; // Handle unset values
            } else {
                $row[$key] = (string) $value; // Explicitly cast to string
            }
        }

        // Remove the leading single quote if it was added
        foreach ($row as $key => $value) {
            if (is_string($value) && !empty($value) && $value[0] === "'") {
                $row[$key] = substr($value, 1);
            }
        }

        return new Site([
            'domain_name' => trim($row['domain_name']),
            'site_url' => trim($row['site_url']),
            'db_name' => encrypt(trim($row['database_name'])),
            'db_username' => encrypt(trim($row['database_username'])),
            'db_password' => encrypt(trim($row['database_password'])),
            'wp_prefix' => encrypt(trim($row['wp_prefix'])),
            'server' => $this->sheetName,
            'niche' => trim($row['niche'])
        ]);
    }

    public function onUnknownSheet($sheetName)
    {
        // Handle unknown sheet
    }
}


// class SitesImport implements WithMultipleSheets, SkipsUnknownSheets
// {
//     public function sheets(): array
//     {
//         return [
//             0 => new PerSheetImport('goku'),
//             1 => new PerSheetImport('homelander'),
//             2 => new PerSheetImport('johnwick'),
//             3 => new PerSheetImport('jonsnow'),
//             4 => new PerSheetImport('mrbean'),
//             5 => new PerSheetImport('obsidian'),
//             6 => new PerSheetImport('onepiece'),
//             7 => new PerSheetImport('pikachu'),
//             8 => new PerSheetImport('saitama'),
//             9 => new PerSheetImport('thor'),
//             10 => new PerSheetImport('thanos'),
//             11 => new PerSheetImport('saphire'),
//             12 => new PerSheetImport('gather'),
//         ];
//     }

//     public function onUnknownSheet($sheetName)
//     {
//         info("Sheet '{$sheetName}' was skipped because it is unknown.");
//     }
// }

// class PerSheetImport implements ToModel, WithHeadingRow
// {
//     use Importable;

//     public $sheetName;

//     public function __construct($sheetName = null)
//     {
//         $this->sheetName = $sheetName;
//     }
//     public function headingRow(): int
//     {
//         return 1;
//     }

//     public function model(array $row)
//     {

//         return new Site([
//             'domain_name' => $row['domain_name'],
//             'site_url' => $row['site_url'],
//             'db_name' => encrypt($row['database_name']),
//             'db_username' => encrypt($row['database_username']),
//             'db_password' => encrypt($row['database_password']),
//             'wp_prefix' => encrypt($row['wp_prefix']),
//             'server' => $this->sheetName
//         ]);
//     }
// }