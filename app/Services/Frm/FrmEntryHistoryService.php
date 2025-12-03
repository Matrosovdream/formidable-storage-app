<?php

namespace App\Services\Frm;

use Illuminate\Support\Facades\DB;
use Throwable;
use App\Repositories\Frm\FrmEntryHistoryRepo;
use App\Repositories\Frm\FrmEntryUpdateTypeRepo;
use App\Repositories\Frm\FrmFieldRepo;

class FrmEntryHistoryService {

    protected $historyRepo;
    protected $updateTypeRepo;
    protected $fieldRepo;

    public function __construct() {
        $this->historyRepo = new FrmEntryHistoryRepo();
        $this->updateTypeRepo = new FrmEntryUpdateTypeRepo();
        $this->fieldRepo = new FrmFieldRepo();
    }

    public function getEntryHistory(int $entry_id, array $site)
    {

        $types = $this->getUpdateTypes();
        $fieldsMap = $this->getFieldsMap($site);

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

                // Update type
                $item['update_type'] = $types[ $item['update_type_id'] ] ?? 'unknown';
                unset( $item['update_type_id'] );

                // Field info
                $item['field'] = $fieldsMap[ $item['field_id'] ] ?? null;

                $items[] = $item;
            }

            return $items;
        }

    }

    public function getFieldsMap(array $site): array
    {

        $site_id = $site['id'];
        $fieldsRaw = $this->fieldRepo->model
            ->where('site_id', $site_id)
            ->get();

        $fields = [];
        foreach ( $fieldsRaw as $field ) {
            $fields[ $field->field_id ] = [
                'key'   => $field->key,
                'type'  => $field->type,
                'label' => $field->label
            ];
        }

        return $fields;

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
                'change_date'    => $item['change_date'],
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
                'change_date'    => $item['change_date']
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
                        'change_date' => $entryData['change_date']
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