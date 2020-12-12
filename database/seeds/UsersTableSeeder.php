<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Traits\GenerateRandomString;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Ադմին',
            'email' => 'admin' . '@gmail.com',
            'password' => bcrypt('#&PnMgTJEy>JY&78'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
