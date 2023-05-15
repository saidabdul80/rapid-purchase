<?php

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

function generatePatientID(){
    return date("ym").rand(100, 999).date('is');
}

function filterBaseUrl($url){
    $u = explode('apis', $url);
    return $u[0];
}

function generateCodeChallenge(){
    return str_replace('=','',base64_encode(hash('sha256', md5(rand(1000,10000)))));
}

function processAccessToken($baseUrl,$data,$code_challenge,$client_id,$secrete_key,$organization_id,$apis_name='emr'){

    try{        
    
         $response = Http::withOptions([
            'debug' => fopen('php://stderr', 'w'),        
        ])->withHeaders([
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36',
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9'
        ])->withBasicAuth($client_id,$secrete_key)->asForm()->post($baseUrl,$data);                                                    
        
        if($response->status() == 200){
            $response = $response->json();            
            $date = Carbon::now();
            $user_id = "";
            if($apis_name =='emr'){
                /* 
                 * @param type $user_id - it represent patient id                *
                 */
                $user_id = $response['patient'];                
                $p_record = [
                    "emr_pid" => $user_id,
                    "organization_id"=>$organization_id,
                ];
            }else{                
                /* 
                 * @param type $user_id - it represent patient id                *
                 */
                $user_id = session('emr_pid');
                $apis_user_id = $response['user_id'];
            }

            $apis_record = [                                      
                "access_token"  => $response['access_token'], 
                "refresh_token" => $response['refresh_token'],    
                "access_token_expiry_date" => $date->addSeconds($response['expires_in']),  
                "scope" => $response['scope'],
                "code_challenge"=>$code_challenge,
                "name"=>$apis_name
            ];
          
            $patient = Patient::where(["emr_pid" =>$user_id])->first();
            if($apis_name == "emr"){
                if(!empty($patient)){
                    unset($apis_record["code_challenge"]);
                    DB::table('apis')->where(["pid" => $patient->id,'name'=>'emr'])->update($apis_record);               
                }else{
                    $patient = Patient::create($p_record);
                    $apis_record["pid"] =  $patient->id;
                    DB::table('apis')->insert($apis_record);
                }           
            }else{
                $apis = DB::table('apis')->where(["pid" => $patient->id,'name'=>$apis_name])->first();
                unset($apis_record["code_challenge"]);//code challenge only for emr type
                $apis_record['user_id'] =$apis_user_id;
                if(!empty($apis)){
                    DB::table('apis')->where(["pid" => $patient->id,'name'=>$apis_name])->update($apis_record);               
                }else{                    
                    $apis_record["pid"] = $patient->id;
                    DB::table('apis')->insert($apis_record);
                }           
            }
            
            Session::put('emr_pid',$user_id);
            return [
                'code_challenge' => $patient->emr->code_challenge,
                'error' => false,
                "message"=>'',   
                "response"=>$response           
            ];
        }else{
            
            return [
                "error"=>true,
                "message" =>"Encounter an error: Please try again",
                "code_challenge"=>'',
                "response"=>$response
            ];
        }  
    }catch(\Exception $e){
        return [
            "error"=>true,
            "message" =>$e->getMessage(),
            "code_challenge"=>''
        ];        
        //$errorMessage = "Encounter an error2: Please try again";
    }    
}

function fetchData($url,$credentials){
    
    $response = Http::withOptions([
        'debug' => fopen('php://stderr', 'w'),        
    ])->withHeaders([
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36',
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'authorization'=> 'Bearer '.$credentials->access_token
    ])->get($url);                
    if($response->status() == 200){
        return $response->json();
    }else{
        return '';
    }
}

function postData($url,$data,$credentials){
    $response = Http::withOptions([
        'debug' => fopen('php://stderr', 'w'),        
    ])->withHeaders([
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.104 Safari/537.36',
        'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'authorization'=> 'Bearer '.$credentials->access_token
    ])->post($url, $data);       
    dd($response);         
    if($response->status() == 200){
        return $response->json();
    }else{
        return '';
    }
}