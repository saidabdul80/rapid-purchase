<?php
use App\Models\Organization;

$fitbit_authorization_link = "https://www.fitbit.com/oauth2/authorize?response_type=code&client_id=238ZZB&scope=activity+cardio_fitness+electrocardiogram+heartrate+nutrition+oxygen_saturation+respiratory_rate+sleep+social+temperature+weight&code_challenge=8_b0QCASCO_nhvaKMqph4kSmGeou48hfW5EdbjPi0EU&code_challenge_method=S256&state=481i1j0i2k3p711s0p0m14015n093a18";
$aud = $_GET['aud'];

$baseUrl = filterBaseUrl($aud);
$org = Organization::where('base_url', $aud)->first();
if(empty($org)){
    abort(403,'This app is not registered');
}
$client_id = $org->client_id;
$state = $aud;//hash('sha256', md5(rand(1000,10000)));

$authorization_link = "$baseUrl/oauth2/default/authorize?client_id=$client_id&response_type=code&scope=launch%2Fpatient%20openid%20fhirUser%20offline_access%20patient%2FAllergyIntolerance.read%20patient%2FCarePlan.read%20patient%2FCareTeam.read%20patient%2FCondition.read%20patient%2FDevice.read%20patient%2FDiagnosticReport.read%20patient%2FDocumentReference.read%20patient%2FEncounter.read%20patient%2FGoal.read%20patient%2FImmunization.read%20patient%2FLocation.read%20patient%2FMedication.read%20patient%2FMedicationRequest.read%20patient%2FObservation.read%20patient%2FOrganization.read%20patient%2FPatient.read%20patient%2FPractitioner.read%20patient%2FProcedure.read%20patient%2FProvenance.read&redirect_uri=http://localhost:8000/home&state=$state";

?>

@extends('layouts.app')
@section('content')
<style>
    body {
        font-family: 'Nunito', sans-serif;
        height: 100vh;
        background-color: white;        
    }

    .cot {
     /*     */
        /* height: 450px;  */
    }
</style>
<div class="row w-100 pl-0">
    <div class="col-md-7" style="width:50%;height: 90vh; background-image: url(/pix/smart.png);background-size: 120%;background-position-x: -62px;background-repeat: no-repeat;"></div> 
    <div  class="col-md-7" style="width:40%;" class="my-3 bg-white shadow-lg ">
        <div class="d-flex justify-content-center align-items-center flex-column" style="height: 450px;">            
            <div class="bg-white shadow-lg p-3 rounded mr-3">            
                <h5 style="font-weight: bold;" class="text-center my-4 text-danger">Allow This app to Update your health Record</h5>                
                <button id="emrAuth" class="w-100 btn btn-primary my-2">Continue</button>
                <!-- {{ json_encode($_COOKIE) }}  
                <input id="email" class="form-control" type="text" placeholder="email*"> -->
                <!-- <a target="_blank"  class="w-100 btn btn-primary my-2" href="{{$authorization_link}}">Continue</a>      -->
            </div>            
        </div>
    </div>    
</div>
<script>
    (function() {
        document.getElementById('emrAuth').onclick = function() {            
            let  pid = '<?=generatePatientID()?>';
            localStorage.setItem('pid',pid);
            localStorage.setItem('base_url','<?= $aud ?>');            
            window.open('<?= $authorization_link ?>', '_self')
            /* axios.post('/create_patient', {pid:pid,organization_id:<?=$org->id ?>}).then(function(res){                                
                if(res.status == 200){
                }
            }); */
        }
    })()
</script>
@endsection