<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\Frm\EntryUpdateTypeSeeder;
use Database\Seeders\User\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call([

            // Frm
            EntryUpdateTypeSeeder::class,
            UserSeeder::class,
        ]);

    }
}
