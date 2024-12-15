<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use App\Services\DnsSetupService;
use Exception;

class DnsSetupController extends Controller
{

    public function create()
    {
        $servers = Server::all();
        return view('dns.create', compact('servers'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'domains' => 'required|string',
            'server' => 'required|exists:servers,id',
        ]);

        $domains = preg_split('/\r\n|\r|\n/', trim($request->input('domains')));
        $domains = array_filter($domains);
        // to prevent time out issue if user pass many web urls and linode take time
        set_time_limit(0);

        $server = Server::find($request->input('server'));

        $dnsService = new DnsSetupService(env('LINODE_API_KEY'));
        $successfulCreated = [];
        foreach ($domains as $domain) {
            $output = $dnsService->createDnsEntry(str_replace(' ', '', $domain), $server);
            $successfulCreated[] = $domain . $output;
        }

        return redirect()->back()->with('dns_success', $successfulCreated);
    }
}
