<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'name'      => 'Demo Company',
            'slug'      => 'demo-company',
            'email'     => 'company@demo.com',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name'       => 'Admin User',
            'email'      => 'admin@demo.com',
            'password'   => Hash::make('password'),
            'company_id' => $company->id,
            'is_active'  => true,
        ]);

        $admin->assignRole('admin');
    }
}