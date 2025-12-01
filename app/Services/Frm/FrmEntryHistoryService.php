<?php

namespace App\Services\Frm;

use Illuminate\Support\Facades\DB;
use Throwable;
use App\Repositories\Frm\FrmEntryHistoryRepo;

class FrmEntryHistoryService {

    protected $historyRepo;

    public function __construct() {
        $this->historyRepo = new FrmEntryHistoryRepo();
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