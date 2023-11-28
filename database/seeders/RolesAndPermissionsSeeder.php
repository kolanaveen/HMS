<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Permission::truncate();
        Role::truncate();

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Doctor']);
        Role::create(['name' => 'Patient']);
        Role::create(['name' => 'Staff']);

        Schema::enableForeignKeyConstraints();
    }
}
