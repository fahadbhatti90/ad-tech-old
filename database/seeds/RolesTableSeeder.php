<?php

use Illuminate\Database\Seeder;
use App\Models\UserRolesModels\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //echo 'test';
       // exit;
        Role::truncate();
        Role::create(['name'=>'Super Admin']);
        Role::create(['name'=>'Admin']);
        Role::create(['name'=>'Manager']);

    }
}
