<?php

namespace Database\Seeders\Site;

use Illuminate\Database\Seeder; 
use App\Models\Site\SiteToken;

class SiteTokenSeeder extends Seeder {

    public function run() {

        $items = $this->getItems();

        foreach ($items as $item) {
            SiteToken::firstOrCreate(
                [ 'token' => $item['token'] ], 
                $item);
        }

    }

    protected function getItems() {
        return [
            ['site_id' => 1, 'token' => '123', 'valid_until' => null ],
        ];
    }

}