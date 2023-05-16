<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /* `ussd`.`users` */
        $users = array(
            array('id' => '1','salute' => 'Mr','first_name' => 'Said','other_name' => 'Abdul','last_name' => 'Abdul','email' => 'saidabdulsalam5@gmail.com','phone_number' => '08065251757','address' => 'S.w 309 kwangila road','user_type' => 'user','password' => '','email_verified_at' => NULL,'remember_token' => NULL,'created_at' => NULL,'updated_at' => NULL,'ussd' => ''),
            array('id' => '14','salute' => 'Mr','first_name' => 'Said2','other_name' => 'Abdul','last_name' => 'Abdul','email' => 'saidabdulsalam05@gmail.com','phone_number' => '08065251752','address' => 'S.w 309 kwangila road','user_type' => 'owner','password' => '','email_verified_at' => NULL,'remember_token' => NULL,'created_at' => NULL,'updated_at' => NULL,'ussd' => ''),
            array('id' => '15','salute' => 'Mr','first_name' => 'Said4','other_name' => 'Abdul','last_name' => 'Abdul','email' => 'saidabdulsalam5@gmail.com','phone_number' => '08096642065','address' => 'S.w 309 kwangila road','user_type' => 'customer','password' => '','email_verified_at' => NULL,'remember_token' => NULL,'created_at' => NULL,'updated_at' => NULL,'ussd' => '*384*88020#'),
            array('id' => '16','salute' => 'Mr','first_name' => 'Said3','other_name' => 'Abdul','last_name' => 'Abdul','email' => 'zendmail05@gmail.com','phone_number' => '08065251758','address' => 'S.w 309 kwangila road','user_type' => 'owner','password' => '','email_verified_at' => NULL,'remember_token' => NULL,'created_at' => NULL,'updated_at' => NULL,'ussd' => '')
        );
  
          User::insert($users);
    }
}
