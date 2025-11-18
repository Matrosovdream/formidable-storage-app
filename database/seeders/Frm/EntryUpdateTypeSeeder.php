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
            ['id' => 1, 'code' => 'create', 'title' => 'Create'],
            ['id' => 2, 'code' => 'update', 'title' => 'Update'],
            ['id' => 3, 'code' => 'delete', 'title' => 'Delete'],
        ];
    }

}