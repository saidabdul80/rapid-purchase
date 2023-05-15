<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Patient;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //   $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    function uuid_to_bin($uuid){
        return pack("H*", str_replace('-', '', $uuid));
    }
    function bin_to_uuid($bin){
        return join("-", unpack("H8time_low/H4time_mid/H4time_hi/H4clock_seq_hi/H12clock_seq_low", $bin));
    }
    public function fetchToken(Request $request)
    {
        try {

            $token = $request->get('token') ?? "";
            $org  = Organization::where('token', $token)->first();
            if (empty($org)) {
                throw new \Exception('Authorization failed', 401);
            }
            $apis = config('emr.apis');
            $patients = Patient::where('organization_id', $org->id)->get();
            $patient_ids = Patient::where('organization_id', $org->id)->pluck('emr_pid');
            if (count($patients) < 1) {
                return response(['patients' => [],'error' => true, "message" => 'no patient found'], 200);
            }
            foreach ($patients as $key => $patient) {
                if($patient->emr != null){
                    
                    $client_id = $org->client_id;
                    $secrete_key = $org->secrete_key;
                    $data = [
                        "grant_type" => "refresh_token",
                        "refresh_token" => $patient->emr->refresh_token,
                        "client_id" => $client_id
                    ];
                    $url = parse_url($org->base_url);
                    $base_url = $url['host'];
                    
                    if (str_contains($base_url, 'localhost')) {
                        $url = explode('/apis', $org->base_url);
                        $base_url = $url[0];
                    }
                    
                    $response = processAccessToken($base_url . '/' . $apis['emr_auth_url'], $data, '', $client_id, $secrete_key, $org->id, 'emr');
                    $emr_response = $response['response'];
                    //dd($response);
                    $patient->emr->refresh_token =  $emr_response['refresh_token'];
                    $patient->emr->access_token =  $emr_response['access_token'];
                }
                //processFitbit($patient);  
            }
            $pids = [];
           /*  foreach($patient_ids as $pid){
                $pids[] = $this->uuid_to_bin($pid);
            } */
            return  response(['patients' => $patients,'pids'=>$patient_ids, 'error' => false, "message" => 'success'],200);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
