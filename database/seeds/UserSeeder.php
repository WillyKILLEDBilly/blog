<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker\Factory::create();

        for($i=0; $i<100; $i++)
        {
        	User::create([
        		'name' 		=> $faker->userName,
        		'email' 	=> $faker->email,
        		'password' 	=> app('hash')->make($faker->word),
        		'activated'	=> rand(0, 1)
        	]);
        }
    }
}