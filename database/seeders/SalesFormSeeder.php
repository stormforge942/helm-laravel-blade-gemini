<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalesFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sales_form')->truncate();

        DB::table('sales_form')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '123-456-7890',
                'website_rented' => 'example.com',
                'price' => 1000,
                'sales_representative' => 'Alice Smith',
                'originating_lead' => 'Google Ads',
                'select_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '987-654-3210',
                'website_rented' => 'sample.com',
                'price' => 1500,
                'sales_representative' => 'Bob Johnson',
                'originating_lead' => 'LinkedIn',
                'select_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@example.com',
                'phone' => '555-123-4567',
                'website_rented' => 'test.com',
                'price' => 2000,
                'sales_representative' => 'Carol White',
                'originating_lead' => 'Referral',
                'select_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'alice.johnson1@example.com',
                'phone' => '111-111-1111',
                'website_rented' => 'site1.com',
                'price' => 1200,
                'sales_representative' => 'Eric Stohl',
                'originating_lead' => 'Google Ads',
                'select_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Smith',
                'email' => 'bob.smith1@example.com',
                'phone' => '111-111-1112',
                'website_rented' => 'site2.com',
                'price' => 1300,
                'sales_representative' => 'Eric Stohl',
                'originating_lead' => 'LinkedIn',
                'select_date' => Carbon::now()->subDays(9)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Charlie',
                'last_name' => 'Brown',
                'email' => 'charlie.brown1@example.com',
                'phone' => '111-111-1113',
                'website_rented' => 'site3.com',
                'price' => 1400,
                'sales_representative' => 'Eric Stohl',
                'originating_lead' => 'Referral',
                'select_date' => Carbon::now()->subDays(8)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Davis',
                'email' => 'david.davis1@example.com',
                'phone' => '111-111-1114',
                'website_rented' => 'site4.com',
                'price' => 1500,
                'sales_representative' => 'Eric Stohl',
                'originating_lead' => 'Facebook',
                'select_date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Eve',
                'last_name' => 'Evans',
                'email' => 'eve.evans1@example.com',
                'phone' => '111-111-1115',
                'website_rented' => 'site5.com',
                'price' => 1600,
                'sales_representative' => 'Eric Stohl',
                'originating_lead' => 'Twitter',
                'select_date' => Carbon::now()->subDays(6)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // Jonathan Gerami - 3 records
            [
                'first_name' => 'Fiona',
                'last_name' => 'Johnson',
                'email' => 'fiona.johnson1@example.com',
                'phone' => '222-222-2221',
                'website_rented' => 'site6.com',
                'price' => 1700,
                'sales_representative' => 'Jonathan Gerami',
                'originating_lead' => 'Instagram',
                'select_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'George',
                'last_name' => 'White',
                'email' => 'george.white1@example.com',
                'phone' => '222-222-2222',
                'website_rented' => 'site7.com',
                'price' => 1800,
                'sales_representative' => 'Jonathan Gerami',
                'originating_lead' => 'Facebook',
                'select_date' => Carbon::now()->subDays(4)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Hannah',
                'last_name' => 'Lee',
                'email' => 'hannah.lee1@example.com',
                'phone' => '222-222-2223',
                'website_rented' => 'site8.com',
                'price' => 1900,
                'sales_representative' => 'Jonathan Gerami',
                'originating_lead' => 'LinkedIn',
                'select_date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // Stephen Griffis - 4 records
            [
                'first_name' => 'Ivy',
                'last_name' => 'Green',
                'email' => 'ivy.green1@example.com',
                'phone' => '333-333-3331',
                'website_rented' => 'site9.com',
                'price' => 2000,
                'sales_representative' => 'Stephen Griffis',
                'originating_lead' => 'Twitter',
                'select_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Jack',
                'last_name' => 'Black',
                'email' => 'jack.black1@example.com',
                'phone' => '333-333-3332',
                'website_rented' => 'site10.com',
                'price' => 2100,
                'sales_representative' => 'Stephen Griffis',
                'originating_lead' => 'Instagram',
                'select_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Karen',
                'last_name' => 'Brown',
                'email' => 'karen.brown1@example.com',
                'phone' => '333-333-3333',
                'website_rented' => 'site11.com',
                'price' => 2200,
                'sales_representative' => 'Stephen Griffis',
                'originating_lead' => 'Referral',
                'select_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'first_name' => 'Liam',
                'last_name' => 'Davis',
                'email' => 'liam.davis1@example.com',
                'phone' => '333-333-3334',
                'website_rented' => 'site12.com',
                'price' => 2300,
                'sales_representative' => 'Stephen Griffis',
                'originating_lead' => 'Google Ads',
                'select_date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}
