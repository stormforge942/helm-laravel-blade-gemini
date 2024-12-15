<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UpdateDnsController extends Controller
{
    public function index()
    {
        return view('maintenance.updatedns.index');
    }

    public function UpdateDns(Request $request)
    {
        $validated = $request->validate([
            'domains' => 'required|string'
        ]);
        $domains = $request->domains;
        $domainList = preg_split('/\r\n|\r|\n/', $domains);
        $domainList = array_map('strtolower', $domainList);
        // API endpoint
        $url = 'https://api.namecheap.com/xml.response';
        $params = [
            'ApiUser' => env('NAMECHEAP_API_USER'),
            'ApiKey' => env('NAMECHEAP_API_KEY'),
            'UserName' => env('NAMECHEAP_API_USER'),
            'ClientIp' => env('NAMECHEAP_API_IP'),
            'Command' => 'namecheap.domains.dns.setCustom',
            'NameServers' => 'ns1.linode.com,ns2.linode.com,ns3.linode.com,ns4.linode.com,ns5.linode.com'
        ];
        $failed = [];
        $success = [];
        foreach ($domainList as $domain) {
            // Parameters (customize as per your needs)
            $splitDomain = explode('.', $domain);
            $params['SLD'] = $splitDomain[0];
            $params['TLD'] = $splitDomain[1];
            // Make the GET request
            $response = Http::get($url, $params);

            // Check if the request was successful
            if ($response->successful()) {
                $xmlResponse = simplexml_load_string($response->body());
                // Handle the response (XML)
                $responseBody = $response->body();
                // Check for errors in the XML response
                if (isset($xmlResponse->Errors->Error)) {
                    // Extract and handle the error messages
                    foreach ($xmlResponse->Errors->Error as $error) {
                        $failed[] = "Error Number: " . (string)$error['Number'] . " - " . (string)$error . " (Domain: " . (string)$domain . ")";
                    }
                } else {
                    $success[] = "$domain Updated Successfully";
                }
            } else {
                // Handle the error
                return response('Request failed: ' . $response->status(), $response->status());
            }
            // If no errors, return the successful response

        }
        return view('maintenance.updatedns.index', compact('failed', 'success'));
    }
}
