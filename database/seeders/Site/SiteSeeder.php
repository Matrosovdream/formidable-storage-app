<?php

namespace Database\Seeders\Site;

use Illuminate\Database\Seeder; 
use App\Models\Site\Site;

class SiteSeeder extends Seeder {

    public function run() {

        $items = $this->getItems();

        foreach ($items as $item) {
            Site::firstOrCreate(
                [ 'url' => $item['url'] ], 
                $item);
        }

    }

    protected function getItems() {
        return [
            ['url' => 'https://example.com', 'name' => 'Example Site' ],
        ];
    }

}