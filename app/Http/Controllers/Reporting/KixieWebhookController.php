<?php

namespace App\Http\Controllers\Reporting;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessKixieCallData;
use App\Jobs\ProcessKixieSmsData;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KixieWebhookController extends Controller
{
    protected $kixieSecret;

    public function __construct()
    {
        $this->kixieSecret = env('KIXIE_SECRET');
    }

    private function verifyKixieSecret($request)
    {
        $headerSecret = $request->header('X-Kixie-Secret');
        return $headerSecret === $this->kixieSecret;
    }

    public function handleCallLog(Request $request)
    {
        if (!$this->verifyKixieSecret($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->input('data.callDetails');

        ProcessKixieCallData::dispatch([
            'callid' => $data['callid'],
            'businessid' => $data['businessid'],
            'calldate' => Carbon::parse($data['calldate'])->format('Y-m-d H:i:s') ?? null,
            'fromnumber' => $data['fromnumber'],
            'tonumber' => $data['tonumber'],
            'duration' => $data['duration'],
            'amount' => $data['amount'],
            'externalid' => $data['externalid'],
            'calltype' => $data['calltype'],
            'callstatus' => $data['callstatus'],
            'recordingurl' => $data['recordingurl'] ?? null,
            'recordingsid' => $data['recordingsid'] ?? null,
            'tonumber164' => $data['tonumber164'],
            'fromnumber164' => $data['fromnumber164'],
            'disposition' => $data['disposition'] ?? null,
            'fname' => $data['fname'] ?? null,
            'lname' => $data['lname'] ?? null,
            'calleridName' => $data['calleridName'] ?? null,
            'email' => $data['email'] ?? null,
            'destinationName' => $data['destinationName'] ?? null,
            'cadenceactionprocessid' => $data['cadenceactionprocessid'] ?? null,
            'powerlistid' => $data['powerlistid'] ?? null,
            'HScalltype' => $data['HScalltype'] ?? null,
            'powerlistsessionid' => $data['powerlistsessionid'] ?? null,
            'extensionDial' => $data['extensionDial'] ?? null,
            'toExt' => $data['toExt'] ?? null,
            'fromExt' => $data['fromExt'] ?? null,
            'answerDate' => Carbon::parse($data['answerDate'])->format('Y-m-d H:i:s') ?? null,
            'callEndDate' => Carbon::parse($data['callEndDate'])->format('Y-m-d H:i:s') ?? null,
            'externalcrmid' => $data['externalcrmid'] ?? null,
            'crmlink' => $data['crmlink'] ?? null,
            'contactid' => $data['contactid'] ?? null,
            'dealid' => $data['dealid'] ?? null,
            'webhookurl' => $data['webhookurl'] ?? null,
            'outcome' => $data['outcome'] ?? null,
        ]);

        return response()->json(['status' => 'Dispatched successfully'], 200);
    }

    public function handleSMSLog(Request $request)
    {
        if (!$this->verifyKixieSecret($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->input('data');

        ProcessKixieSmsData::dispatch([
            'messageid' => $data['messageid'] ?? null,
            'from' => $data['from'] ?? null,
            'customernumber' => $data['customernumber'] ?? null,
            'to' => $data['to'] ?? null,
            'businessnumber' => $data['businessnumber'] ?? null,
            'direction' => $data['direction'] ?? null,
            'message' => $data['message'] ?? null,
            'businessid' => $data['businessid'] ?? null,
            'userid' => $data['userid'] ?? null,
            'deal_stage' => $data['contact']['deal']['stage'] ?? null,
            'deal_title' => $data['contact']['deal']['title'] ?? null,
            'deal_value' => $data['contact']['deal']['value'] ?? null,
            'deal_status' => $data['contact']['deal']['status'] ?? null,
            'contact_firstName' => $data['contact']['contact']['firstName'] ?? null,
            'contact_lastName' => $data['contact']['contact']['lastName'] ?? null,
            'contact_address' => $data['contact']['contact']['address'] ?? null,
            'contact_phone' => $data['contact']['contact']['phone'] ?? null,
            'contact_city' => $data['contact']['contact']['city'] ?? null,
            'contact_name' => $data['contact']['contact']['name'] ?? null,
            'contact_link' => $data['contact']['contact']['link'] ?? null,
            'contact_id' => $data['contact']['contact']['id'] ?? null,
            'contact_email' => $data['contact']['contact']['email'] ?? null,
            'contact_success' => $data['contact']['success'] ?? null,
            'device_type' => $data['contact']['device']['type'] ?? null,
            'device_usecase' => $data['contact']['device']['usecase'] ?? null,
            'device_isactive' => $data['contact']['device']['isactive'] ?? null,
            'device_pushid' => $data['contact']['device']['pushid'] ?? null,
            'crmlink' => $data['crmlink'] ?? null,
            'email'=>$data['email'] ?? 'unknown',
        ]);

        return response()->json(['status' => 'Dispatched successfully'], 200);
    }
}
