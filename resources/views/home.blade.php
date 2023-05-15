<?php
header('P3P: CP=HONK');

use App\Models\Apis;
use App\Models\Organization;
use App\Models\Patient;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

$code = $_GET['code'];   //emr code 
$baseUrl =filterBaseUrl($_GET['state']);   //emr code 
$org = Organization::where('base_url',$_GET['state'])->first();    
$errorMessage = "";
$emr_pid = "";
$code_challenge =generateCodeChallenge(); //emr_pid hash
$data = [
    "code"=>$code,
    "client_id"=>$org->client_id,
    "grant_type"=>"authorization_code",
    "redirect_uri"=>urldecode("http://localhost:8000/home")
 ]; 
$response = processAccessToken("$baseUrl/oauth2/default/token",$data,$code_challenge,$org->client_id,$org->secrete_key,$org->id,'emr');
/* try{
    $data = [
       "code"=>$code,
       "client_id"=>"qZ45JJ2fGttCHNOtGIOwSAxKBfdkUXim-N2FVf-nZSo",
       "grant_type"=>"authorization_code",
       "redirect_uri"=>urldecode("http://localhost:8000/home")
    ]; 

     $response = Http::withBasicAuth($org->client_id,$org->secrete_key)
                        ->asForm()
                        ->post("$baseUrl/oauth2/default/token",$data);      
      
    if($response->getStatusCode() == 200){
        $response = $response->json();
        $date = Carbon::now();
        $emr_pid = $response['patient'];
        $p_record = [
             "emr_pid" => $emr_pid,
             "organization_id"=>$org->id,
        ];
        $emr_record = [                      
            "refresh_token_expiry_date"=> $date->addMonths(3),             
            "access_token"  => $response['access_token'], 
            "refresh_token" => $response['refresh_token'],    
            "access_token_expiry_date" => $date->addSeconds($response['expires_in']),  
            "scope" => $response['scope'],
            "code_challenge"=>$code_challenge,
            "name"=>"emr"
        ];
      
        $patient = DB::table('patients')->where(["emr_pid" => $response['patient']])->first();
        if(!empty($patient)){
           DB::table('apis')->where(["pid" => $patient->id,'name'=>'emr'])->update($emr_record);
        }else{
            $p = Patient::create($p_record);
            $emr_record["pid"] = $p->id;
            DB::table('apis')->insert($emr_record);
        }           
    }else{
        $errorMessage = "Encounter an error: Please try again";
    }  
}catch(\Exception $e){
    $errorMessage = $e->getMessage();
    //$errorMessage = "Encounter an error2: Please try again";
} */


$code_challenge = $response['code_challenge'];
$authorization_link = "https://www.fitbit.com/oauth2/authorize?response_type=code&client_id=".env("FITBIT_CLIENT_ID")."&scope=activity+cardio_fitness+electrocardiogram+heartrate+nutrition+oxygen_saturation+respiratory_rate+sleep+social+temperature+weight&code_challenge=$code_challenge&code_challenge_method=plain&state=481i1j0i2k3p711s0p0m14015n093a18";                
?> 
@extends('layouts.app')
@section('content')
<div style="display: none;" id="cardx">
<center>
    @include('output_temp',["response"=>$response,"message"=>"Access Granted Successfully.", "close"=>false, "authorization_link"=>$authorization_link])
</center>
</div>
<style>
    body{
        background-color: white;
    }
</style>
<script>
    //let pid = localStorage.getItem('pid');
  //  let code = '{{$code}}';    
    function createCookie(name, value, days) {
        var expires;
        if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
        }
        else {
        expires = "";
        }
        document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
    }
    (function(){    
        document.getElementById('loader').style.display = 'block';
        setTimeout(function(){
            document.getElementById('loader').style.display = 'none';
            document.getElementById('cardx').style.display = 'block';
        }, 700)        
        document.getElementById('emrAuth').onclick = function() {            
            window.open('/?aud='+localStorage.getItem('base_url'), '_self')
        }
    })()
</script>
@endsection

