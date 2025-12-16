<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Frm\EntryUpdateTypeSeeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Site\SiteSeeder;
use Database\Seeders\Site\SiteTokenSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([

            // Site
            //SiteSeeder::class,
            //SiteTokenSeeder::class,

            // Frm
            EntryUpdateTypeSeeder::class,
            UserSeeder::class,
        ]);

    }
}
