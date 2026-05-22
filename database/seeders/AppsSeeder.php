<?php

namespace Database\Seeders;

use App\Models\App;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppsSeeder extends Seeder
{
    public function run(): void
    {
        $apps = [
            [
                'name'        => 'Currency Exchange',
                'slug'        => 'currency-exchange',
                'description' => 'Real-time currency rates, charts, filters and statistics.',
                'icon'        => 'currency',
                'color'       => 'blue',
                'is_active'   => true,
            ],
            [
                'name'        => 'HR Management',
                'slug'        => 'hr',
                'description' => 'Manage employees, departments and HR workflows.',
                'icon'        => 'users',
                'color'       => 'green',
                'is_active'   => true,
            ],
            [
                'name'        => 'Accounting',
                'slug'        => 'accounting',
                'description' => 'Invoices, expenses and financial reporting.',
                'icon'        => 'chart',
                'color'       => 'yellow',
                'is_active'   => true,
            ],
            [
                'name'        => 'Reports',
                'slug'        => 'reports',
                'description' => 'Advanced reporting and data analytics.',
                'icon'        => 'trending',
                'color'       => 'purple',
                'is_active'   => true,
            ],
                [
                'name'        => 'Salon & Spa Management',
                'slug'        => 'salon',
                'description' => 'Appointments, services, subscriptions and client management for beauty salons and spas.',
                'icon'        => 'salon',
                'color'       => 'pink',
                'is_active'   => true,
            ],
        ];

        foreach ($apps as $app) {
            App::firstOrCreate(['slug' => $app['slug']], $app);
        }
    }
}