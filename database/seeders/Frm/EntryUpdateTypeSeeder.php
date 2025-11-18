<?php

namespace Database\Seeders\Frm;

use Illuminate\Database\Seeder; 
use App\Models\Frm\FrmEntryUpdateType;

class EntryUpdateTypeSeeder extends Seeder {

    public function run() {

        $items = $this->getItems();

        foreach ($items as $item) {
            FrmEntryUpdateType::firstOrCreate(
                [ 'code' => $item['code'] ], 
                $item);
        }

    }

    protected function getItems() {
        return [
            ['code' => 'create', 'title' => 'Create'],
            ['code' => 'update', 'title' => 'Update'],
            ['code' => 'delete', 'title' => 'Delete'],
        ];
    }

}