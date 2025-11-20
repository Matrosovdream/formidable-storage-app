<?php

namespace Database\Seeders\Site;

use Illuminate\Database\Seeder; 
use App\Services\Site\SiteService;

class SiteSeeder extends Seeder {

    public function __construct(
        protected SiteService $siteService,
    ) {

    }

    public function run() {

        $items = $this->getItems();

        foreach ($items as $item) {
            $this->siteService->createSite($item);
        }

    }

    protected function getItems() {
        return [
            ['url' => 'https://unitedpassport.com', 'name' => 'United Passport' ],
        ];
    }

}