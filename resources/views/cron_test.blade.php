<?php
  use App\Models\Patient;
  $patients = Patient::all();
  $apis = config('emr.apis');
  foreach($patients as $key => $patient){
      $emr = $patient->emr;
      $org = $patient->organization;
      $client_id = $org->client_id;
      $secrete_key = $org->secrete_key;            
        $data = [
            "grant_type"=>"refresh_token",
            "refresh_token"=>$patient->emr->refresh_token,
            "client_id"=>$client_id
        ];
        
        $response = processAccessToken($apis['emr_auth_url'],$data,'',$client_id,$secrete_key,$org->id,'emr');        
        $emr_response = $response['response'];
       //dd($response);
       $patient->emr->access_token =  $emr_response['access_token'];
       processFitbit($patient);

  }

 /* 
  *@return access token 
  */
  function processFitbit(Patient $patient){
        $client_id = env("FITBIT_CLIENT_ID");
        $secrete_key = env("FITBIT_CLIENT_SECRET");
        $apis = config('emr.apis');
        $observations = config('emr.observations'); //names like sleep
        $emrObservationAPIUrl = config('emr.base_url').config('emr.apis.emr.observation');
        $data = [
            "grant_type"=>"refresh_token",
            "refresh_token"=>$patient->fitbit->refresh_token,
            "client_id"=>$client_id
        ];
        $response = processAccessToken($apis['fitbit_auth_url'],$data,'',$client_id,$secrete_key,$patient->organization->id,'fitbit');        
        $patient->fitbit->access_token = $response['response']['access_token'];
        foreach($observations as $observation => $key){
            $url = $apis['fibit'][$observation];
            $url = str_replace('{user_id}',$patient->fitbit->user_id,$url);
            $url = str_replace('{date}','today',$url);                
            $response = fetchData($url,$patient->fitbit);  //fetch record from fitbit                           
            if(!$response == ''){                   
                $records = $response[$key['key']];                
                $data = [
                    "code_type"=>107, //snomed
                    "groupname"=> $observation.'-fitbit',
                    "observation"=> $records,
                    "pid"=> $patient->emr_pid,
                    "ob_type"=>"assessment",
                    "code"=> $key['code'],
                    "date"=> now()
                ];
                $response = postData($emrObservationAPIUrl,$data,$patient->emr,'fibit');
            }
        }
       

      /*   if(!$patient->fitbit->access_token_is_active){
        }else{
            $data = [
                "grant_type"=>"refresh_token",
                "refresh_token"=>$patient->fitbit->refresh_token,
                "client_id"=>$client_id
            ];
            $response = processAccessToken($apis['fitbit_auth_url'],$data,'',$client_id,$secrete_key,$patient->organization->id,'fitbit');        

        } */
  }  
?>