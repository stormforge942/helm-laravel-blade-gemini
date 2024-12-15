<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpImap\Mailbox;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader; 

class DownloadCsvFromEmail extends Command
{
    protected $signature = 'email:download-csv';
    protected $description = 'Download a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Configure mailbox connection
        $mailbox = new Mailbox(
            env('IMAP_HOST'), // IMAP server and mailbox folder
            env('IMAP_USERNAME'),                        
            env('IMAP_PASSWORD'),                        
            env('IMAP_DIRECTORY') // Directory to save attachments
        );

        // Search for the email
        $mailsIds = $mailbox->searchMailbox('FROM "support@kixie.com"');

        if(!$mailsIds) {
            $this->info('No emails found.');
            return 0;
        }

       // Sort the emails by date in descending order to get the latest email first
       rsort($mailsIds);

       // Get the latest email
       $latestEmailId = $mailsIds[0];
       $email = $mailbox->getMail($latestEmailId);

       // Search for the "Report Download" link in the email's HTML content
       if (preg_match('/<a[^>]+href="([^"]+)"[^>]*>Report Download<\/a>/', $email->textHtml, $matches)) {
           $downloadLink = $matches[1];

           // Download the CSV file
           $csvContent = file_get_contents($downloadLink);

           if ($csvContent) {
               // Save the CSV file the folder
               $fileName = 'downloaded-file-' . date('Y-m-d') . '.csv';
               $filePath = storage_path('app/csv/' . $fileName);
               Storage::disk('local')->put('csv/' . $fileName, $csvContent);

               $this->info("CSV file saved as: csv/{$fileName}");

               // Ingest CSV into the kixie_sms_logs_csv table
               $this->ingestCsvIntoDatabase($filePath);
           } else {
               $this->error('Failed to download the CSV file.');
           }
       } else {
           $this->error('No download link found in the email.');
       }

       // Mark email as read
       $mailbox->markMailAsRead($latestEmailId);


       return 0;
   }

   protected function ingestCsvIntoDatabase($filePath)
   {
       // Open the CSV file
       $csv = Reader::createFromPath($filePath, 'r');
       $csv->setHeaderOffset(0); // CSV has a header row

        // Get the header columns from the CSV
        $headers = $csv->getHeader();

        // Define the expected columns
        $expectedColumns = [
            'Date',
            'First Name',
            'Last Name',
            'Type',
            'Internal SMS ID',
            'External Contact',
            'Message',
            'CRM Link',
            'Status'
         ];

       foreach ($csv as $record) {
        $originalDate = in_array('Date', $headers) &&  isset($record['Date']) && !empty($record['Date']) ? 
                        \Carbon\Carbon::createFromFormat('m/d/Y, h:i:s A', $record['Date'])->format('Y-m-d H:i:s') : null;

        // Check for each value, and if the column is missing, set it to null
        $firstName = in_array('First Name', $headers) &&  isset($record['First Name']) && !empty($record['First Name']) ? $record['First Name'] : null;
        $lastName = in_array('Last Name', $headers) && isset($record['Last Name']) && !empty($record['Last Name']) ? $record['Last Name'] : null;
        $type = in_array('Type', $headers) && isset($record['Type']) && !empty($record['Type']) ? $record['Type'] : null;
        $internalSmsId =in_array('Internal SMS ID', $headers) &&  isset($record['Internal SMS ID']) && !empty($record['Internal SMS ID']) ? $record['Internal SMS ID'] : null;
        $externalContact =in_array('External Contac', $headers) &&  isset($record['External Contact']) && !empty($record['External Contact']) ? $record['External Contact'] : null;
        $message = in_array('Message', $headers) && isset($record['Message']) && !empty($record['Message']) ? $record['Message'] : null;
        $crmLink = in_array('CRM Link', $headers) && isset($record['CRM Link']) && !empty($record['CRM Link']) ? $record['CRM Link'] : null;
        $status = in_array('Status', $headers) && isset($record['Status']) && !empty($record['Status']) ? $record['Status'] : null;  // If 'Status' is missing, set it to null

           DB::table('kixie_sms_logs_csv')->insert([
               'date' =>$originalDate,
               'first_name' => $firstName,
               'last_name' => $lastName,
               'type' => $type,
               'internal_sms_id' => $internalSmsId,
               'external_contact' => $externalContact,
               'message' => $message,
               'crm_link' => $crmLink,
               'status' => $status,
               'created_at' => now(),
               'updated_at' => now(),
           ]);

    }

       $this->info('CSV data has been ingested into the kixie_sms_logs_csv table.');
   }
}