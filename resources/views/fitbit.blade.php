<?php
    //$code = $_GET['code'];

use App\Models\Organization;
use App\Models\Patient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
$code = $_GET['code'];
$emr_pid = session('emr_pid');
//echo $emr_pid;
$client_id = env('FITBIT_CLIENT_ID');
$secrete_key = env('FITBIT_CLIENT_SECRET');
$patient = Patient::where('emr_pid',$emr_pid)->first();//loops all patients              
$data = [
    "code"=>$code,
    "client_id"=>$client_id,
    "grant_type"=>"authorization_code",
    "code_verifier"=>$patient->emr->code_challenge,    
]; 

$response = processAccessToken("https://api.fitbit.com/oauth2/token",$data,$patient->emr->code_challenge,$client_id,$secrete_key,$patient->organization_id,'fitbit');
?>
@extends('layouts.app')
@section('content')

<div style="display: none;" id="cardx">
    @include('output_temp',["response"=>$response,"message"=>"Fitbit Access Granted Successfully.", "close"=>true])
</div> 
<script>
    let pid = localStorage.getItem('pid');
    let code = '{{$code}}';
    (function(){
        document.getElementById('loader').style.display = 'block';
        setTimeout(function(){
            document.getElementById('loader').style.display = 'none';
            document.getElementById('cardx').style.display = 'block';
        }, 500)        
                    
    })()
</script>

@endsection