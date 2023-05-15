<?php
    return [
        "base_url" =>"https://emr.vlabnigeria.org",
        "observations"=>[
           "sleep"=>["key"=>"summary","code"=>60746000]
           /*  "heart_rate",
            "blood_pressure" */
        ],

        "apis"=>[
            "emr_auth_url"=>"oauth2/default/token",
            "fitbit_auth_url"=>"https://api.fitbit.com/oauth2/token",
            "emr"=>[
                "observation"=>"​/fhir​/observation"
            ],
            "fibit"=>[
                "sleep"=>"https://api.fitbit.com/1.2/user/{user_id}/sleep/date/{date}.json",
                /* "heart_rate"=>"",
                "activities"=>"" */
            ],
            "googlefit"=>[

            ],
        ]
    ];