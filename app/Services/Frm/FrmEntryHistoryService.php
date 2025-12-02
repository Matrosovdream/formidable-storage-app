<?php

namespace App\Services\Frm;

use Illuminate\Support\Facades\DB;
use Throwable;
use App\Repositories\Frm\FrmEntryHistoryRepo;
use App\Repositories\Frm\FrmEntryUpdateTypeRepo;

class FrmEntryHistoryService {

    protected $historyRepo;
    protected $updateTypeRepo;

    public function __construct() {
        $this->historyRepo = new FrmEntryHistoryRepo();
        $this->updateTypeRepo = new FrmEntryUpdateTypeRepo();
    }

    public function getEntryHistory(int $entry_id, array $site)
    {

        $types = $this->getUpdateTypes();
        
        // Get by entry_id and site_id
        $site_id = $site['id'];
        $history = $this->historyRepo->model
            ->where('entry_id', $entry_id)
            ->where('site_id', $site_id)
            ->get();

        if ($history->isEmpty()) {
            return [];
        } else {

            $itemsRaw = $history->toArray();
            $items = [];
            foreach ( $itemsRaw as $item ) {
                $item['update_type'] = $types[ $item['update_type_id'] ] ?? 'unknown';
                unset( $item['update_type_id'] );
                $items[] = $item;
            }

            return $items;
        }

    }

    public function getUpdateTypes(): array
    {
        $typesRaw = $this->updateTypeRepo->getAll();
        foreach ( $typesRaw['items'] as $type ) {
            $types[ $type['id'] ] = $type['code'];
        }

        return $types;
    }

    public function updateEntryHistory(array $data, array $site): bool
    {
        $site_id  = $site['id'];
        $entry_id = $data['entry_id'];
    
        $entries = [];
    
        // Updated
        foreach ($data['updated'] as $item) {
            $entries[] = [
                'entry_id'       => $entry_id,
                'site_id'        => $site_id,
                'field_id'       => $item['field_id'],
                'update_type_id' => 2, // Updated
                'value'          => $item['value'],
            ];
        }
    
        // Created
        foreach ($data['created'] as $item) {
            $entries[] = [
                'entry_id'       => $entry_id,
                'site_id'        => $site_id,
                'field_id'       => $item['field_id'],
                'update_type_id' => 1, // Created
                'value'          => $item['value'],
            ];
        }
    
        DB::beginTransaction();
    
        try {
    
            foreach ($entries as $entryData) {
                $this->historyRepo->model->updateOrCreate(
                    [
                        'entry_id' => $entryData['entry_id'],
                        'field_id' => $entryData['field_id'],
                        'update_type_id' => $entryData['update_type_id'],
                    ],
                    [
                        'site_id' => $entryData['site_id'],
                        'value' => $entryData['value'],
                    ]
                );
            }
    
            DB::commit();
            return true;
    
        } catch (Throwable $e) {
    
            DB::rollBack();
    
            // Optional: log or rethrow
            report($e);
            // throw $e;
    
            return false;
        }
    }

}