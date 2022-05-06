<?php
use App\Models\AgencyModels\AgencyModel;
use App\Models\CustomUserModel;
use App\Models\UserRoles\UserRole;
use App\Models\UserRolesModels\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class addAdminManagerRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserRole::create([
            'roleId' => 3,
            'userId' => 2
        ]);//end create function

    }
}
