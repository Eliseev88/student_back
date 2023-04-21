<?php

namespace Database\Seeders;

use Database\Factories\UserOrganizerFactory;
use Illuminate\Database\Seeder;

class UserOrganizerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserOrganizerFactory::factoryForModel('UserOrganizer')
        ->count(10)
        ->create();
    }
}
