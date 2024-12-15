<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class DnsSetupService
{
    protected $client;

    public function __construct($apiKey)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.linode.com/v4/',
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function createDomain($data)
    {
        return $this->request('POST', 'domains', $data);
    }

    public function createDomainRecord($domainId, $data)
    {
        return $this->request('POST', "domains/{$domainId}/records", $data);
    }

    private function request($method, $uri, $data)
    {
        $response = $this->client->request($method, $uri, [
            'json' => $data,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function createDnsEntry($domain, $server)
    {
        try {
            $domainResponse = $this->createDomain([
                'domain' => $domain,
                'type' => 'master',
                'tags' => [$server->name],
                'soa_email' => 'accounts@localspark.ai',
            ]);
            $domainId = $domainResponse['id'];

            $this->createDomainRecord($domainId, [
                'type' => 'A',
                'name' => '',
                'target' => $server->ip_address,
            ]);

            $this->createDomainRecord($domainId, [
                'type' => 'A',
                'name' => 'mail',
                'target' => $server->ip_address,
            ]);

            $this->createDomainRecord($domainId, [
                'type' => 'CNAME',
                'name' => 'www',
                'target' => $domain,
            ]);
            return ' (created)';
        } catch (Exception $e) {
            return " (Error creating DNS entry)";
            // error_log('Error creating DNS entry: ' . $e->getMessage());
        }
    }
}
