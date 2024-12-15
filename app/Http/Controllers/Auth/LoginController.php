<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Spatie\Permission\Models\Role;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;


class LoginController extends Controller
{

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the post login redirect path.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->hasAnyRole(['administrator', 'super_admin'])) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    public function redirectToAzure()
    {
        return Socialite::driver('azure')
                ->scopes(['openid','User.ReadBasic.All', 'email', 'profile'])
                ->redirect();
    }

    public function handleAzureCallback()
    {
        if (config('custom.dev_env')) {
            $user = User::where('email', 'developer@gmail.com')->first();

            Auth::login($user, true);

            return redirect()->intended($this->redirectTo);
        }

        try {
        // Get user from Azure AD
        $azureUser = Socialite::driver('azure')->user();

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to authenticate with Azure AD.');
        }

        $idToken = $azureUser->accessTokenResponseBody['id_token'];
       
        $jwtParts = explode('.', $idToken);

        if (count($jwtParts) !== 3) {
            return redirect('/')->with('error', 'Invalid ID token format.');
        }

        // Decode the payload (2nd part of the JWT)
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $jwtParts[1]));

        // Convert JSON payload to array
        $decodedPayload = json_decode($payload, true);

        // Get the email from the decoded payload
        $email = $decodedPayload['email'] ?? $decodedPayload['preferred_username'] ?? null;
                
        if (!$email) {
            return redirect('/')->with('error','Unable to retrieve email address from Azure.');
        }
    
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect('/')->with('error', 'User not found.');
        } else {
            $user->password = '';
            $user->account_type = 'microsoft';
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->intended($this->redirectTo);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
                ->scopes(['email','profile'])
                ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            // dd($googleUser);
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Failed to authenticate with Google.');
        }
    
        $email = $googleUser->email;
        $companyEmail = "@localspark.ai";

        if (str_ends_with($email, $companyEmail)) {
            return redirect('/')->with('error', 'Please use your Microsoft account to log in.');
        }
    
        $user = User::where('email', $email)->first();
    
        if (!$user) {
            return redirect('/')->with('error', 'User not found.');
        }

        $user->account_type = 'google';
        $user->save();

        Auth::login($user);
    
        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->flush();
        $azureLogoutUrl = Socialite::driver('azure')->getLogoutUrl(route('login'));
        return redirect($azureLogoutUrl);
    }
}