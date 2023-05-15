<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user =[     
            ['name' => 'Admin admin','email' => 'admin@gmail.com','password' => Hash::make("password")]
        ];
        User::insert($user);
        DB::select(DB::raw("INSERT INTO `organizations` (`id`, `name`, `client_id`, `secrete_key`, `base_url`, `token_life_time`, `life_time_type`, `created_at`, `updated_at`) VALUES  (1, 'Smart Device', 'qZ45JJ2fGttCHNOtGIOwSAxKBfdkUXim-N2FVf-nZSo', '4JsIz_w77qqMfuKCetLQtBX32Z_8ZfbJsSd2-ReIUkI9rbPxrK3PpgjVCajbgfwN6Qrl5f2HlZJOXWL_uc7J2g', 'https://emr.vlabnigeria.org/apis/default/fhir', 2, 'weeks', NULL, NULL)"));
    }
}
