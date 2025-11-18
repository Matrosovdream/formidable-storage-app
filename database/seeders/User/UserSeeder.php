<?php

namespace Database\Seeders\User;

use App\Models\User;;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $items = $this->getItems();

        foreach ($items as $item) {
            User::firstOrCreate(
                [ 'email' => $item['email'] ], 
                $item);
        }


    }

    protected function getItems() {
        return [
            ['name' => 'Admin User', 'email' => 'matrosovdream@gmail.com', 'password' => Hash::make('123')],
        ];
    }
    
}