<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\Leads;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class LeadsUserController extends Controller
{
    public function redirectToLeadsSite()
    {
        $user = Auth::user();
        $jwtToken = $this->generateJwtToken($user);
    
        $redirectUrl = env('CLEARVUE_SITE_URL');
    
        $queryParams = http_build_query([
            'token' => $jwtToken
        ]);

        $redirectUrl .= '?' . $queryParams;
    
        return redirect()->away($redirectUrl);

    }

    private function generateJwtToken($user)
    {
        return JWTAuth::fromUser($user);
    }
}