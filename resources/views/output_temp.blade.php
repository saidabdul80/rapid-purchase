<div class="d-flex  justify-content-center align-items-center flex-column">
        @if(!$response['error'])
            @if(!$close)    
                <img src="/pix/success.png" width="70px">        
                <div class="alert alert-success my-3" style="font-size: 1em;">
                    <strong>Greate!</strong> {{$message}}
                    <div style="max-width: 450px;" class="my-5 bg-white shadow-lg p-3 rounded">
                        <h5 style="font-weight: bold;" class="text-center my-3">Allow This app to fetch your Record from fitbit</h5>
                        <hr class="mb-4">
                        <a class="btn btn-primary ml-3 w-100" target="_blank" href="{{$authorization_link}}">Continue</a>
                    </div>
                </div>
            @else
                <div  class="d-flex  justify-content-center align-items-center flex-column">
                    <img src="/pix/success.png" width="70px">        
                    <div class="alert alert-success my-3" style="font-size: 1em;">
                        <strong>Greate!</strong>  
                        <div style="max-width: 450px;" class="my-5 bg-white shadow-lg p-3 rounded">
                            <h5 style="font-weight: bold;" class="text-center my-3">Click ok to close this page</h5>
                            <hr class="mb-4">
                            <a onclick="javascript:window.close()" class="btn btn-primary ml-3 w-100">Ok</a>
                        </div>
                    </div>
                </div>
            @endif
        @else
        <img src="/pix/fail2.png" width="70px">        
        <div class="alert alert-danger my-3" style="font-size: 1em;">
            <div style="width: 300px;overflow-x:auto"><strong>Opps!</strong> {{$response['message']}}.</div>
            <div style="max-width: 450px;" class="my-5 bg-white shadow-lg p-3 rounded">                
                <button id="emrAuth" class="btn btn-primary ml-3 w-100" >Try Again</button>
            </div>
        </div>
        @endif
</div>