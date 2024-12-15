<?php 

namespace App\Imports;

use App\Models\WpApiDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Illuminate\Support\Facades\Log;

interface PerSheetImport extends ToModel, WithHeadingRow, SkipsUnknownSheets {}

class WpApiDetailsImport extends BaseImport implements WithMultipleSheets, SkipsUnknownSheets
{
    protected function sheetMapping(): array
    {
        return ['goku', 'homelander', 'johnwick', 'jonsnow', 'mrbean', 'obsidian', 'onepiece', 'pikachu', 'saitama', 'thor', 'thanos', 'saphire', 'gather','hulk','spiderman'];
    }

    protected function createPerSheetImport($sheetName): PerSheetImport
    {
        return new PerSheetImportForWpApiDetails($sheetName);
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

class PerSheetImportForWpApiDetails implements PerSheetImport
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
        // Check if the foreign key values exist in the related table
        $siteUrl = trim($row['site_url'] ?? null);
        $server = $this->sheetName;

        if (!$siteUrl || !$server || !$this->relatedEntryExists($siteUrl, $server)) {
            $this->logError($row, 'Foreign key constraint violation');
            return null;
        }

        try {
            return new WpApiDetail([
                'site_url' => $siteUrl,
                'server' => $server,
                'username' => encrypt(trim($row['username'])),
                'password' => encrypt(trim($row['password'])),
                'application_password' => encrypt(trim($row['application_password'])),
            ]);
        } catch (\Exception $e) {
            $this->logError($row, $e->getMessage());
            return null;
        }
    }

    public function onUnknownSheet($sheetName)
    {
        // Handle unknown sheet
    }

    protected function relatedEntryExists($siteUrl, $server)
    {
        return \DB::table('wordpress_sites')->where('site_url', $siteUrl)->where('server', $server)->exists();
    }

    protected function logError($row, $errorMessage)
    {
        Log::error('Import error', [
            'sheet' => $this->sheetName,
            'row' => $row,
            'error' => $errorMessage,
        ]);
    }
}
