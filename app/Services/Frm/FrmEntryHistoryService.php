<?php

namespace App\Services\Frm;

use Illuminate\Support\Facades\DB;
use Throwable;
use App\Repositories\Frm\FrmEntryHistoryRepo;
use App\Repositories\Frm\FrmEntryUpdateTypeRepo;
use App\Repositories\Frm\FrmFieldRepo;
use App\Services\CacheService;

class FrmEntryHistoryService {

    protected $historyRepo;
    protected $updateTypeRepo;
    protected $fieldRepo;
    protected CacheService $cache;

    public function __construct(?CacheService $cache = null) {
        $this->historyRepo = new FrmEntryHistoryRepo();
        $this->updateTypeRepo = new FrmEntryUpdateTypeRepo();
        $this->fieldRepo = new FrmFieldRepo();
        $this->cache = $cache ?? app(CacheService::class);
    }

    public function getEntryHistory(int $entry_id, array $site)
    {
        $site_id = $site['id'];

        return $this->cache->rememberEntryMeta($site_id, $entry_id, function () use ($entry_id, $site, $site_id) {

            $types = $this->getUpdateTypes();
            $fieldsMap = $this->getFieldsMap($site);

            $history = $this->historyRepo->model
                ->where('entry_id', $entry_id)
                ->where('site_id', $site_id)
                ->orderBy('id', 'desc')
                ->get();

            if ($history->isEmpty()) {
                return [];
            }

            $itemsRaw = $history->toArray();
            $items = [];
            foreach ($itemsRaw as $item) {
                $item['update_type'] = $types[$item['update_type_id']] ?? 'unknown';
                unset($item['update_type_id']);
                $item['field'] = $fieldsMap[$item['field_id']] ?? null;
                $items[] = $item;
            }

            return $items;
        });
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
        $user_id  = $data['user_id'] ?? null;
    
        $entries = [];
    
        // Updated
        if(
            isset($data['updated']) ??
            is_array($data['updated']) ??
            !empty($data['updated'])
        ) {

            foreach ($data['updated'] as $item) {
                $entries[] = [
                    'entry_id'       => $entry_id,
                    'site_id'        => $site_id,
                    'field_id'       => $item['field_id'],
                    'user_id'        => $user_id ?? null,
                    'update_type_id' => 2, // Updated
                    'old_value'          => $item['old_value'],
                    'new_value'          => $item['new_value'],
                    'change_date'    => $item['change_date'],
                ];
            }

        }
    
        // Created
        if( 
            isset($data['created']) && 
            is_array($data['created']) && 
            !empty($data['created']) 
            ) {
             
            foreach ($data['created'] as $item) {
                $entries[] = [
                    'entry_id'       => $entry_id,
                    'site_id'        => $site_id,
                    'field_id'       => $item['field_id'],
                    'user_id'        => $user_id ?? null,
                    'update_type_id' => 1, // Created
                    'old_value'          => $item['old_value'],
                    'new_value'          => $item['new_value'],
                    'change_date'    => $item['change_date']
                ];
            }

        }
    
        DB::beginTransaction();
    
        try {
    
            foreach ($entries as $entryData) {
                $this->historyRepo->model->create(
                    $entryData
                );
            }
    
            DB::commit();

            // Invalidate cached meta for this entry; next read rebuilds.
            $this->cache->forgetEntryMeta($site_id, $entry_id);

            return true;
    
        } catch (Throwable $e) {
    
            DB::rollBack();
    
            // Optional: log or rethrow
            report($e);
            throw $e;
    
            return false;
        }
    }

}