<?php
namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEntryHistory;


class FrmEntryHistoryRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEntryHistory();

    }

    public function updateLogsMultiple( array $data, array $site ) 
    {
        
        $site_id = $site['id'];
        $logs = $data['logs'] ?? [];

        foreach ( $logs as $logData ) {
            $entry_id       = $logData['entry_id'] ?? null;
            $field_id       = $logData['field_id'] ?? null;
            $user_id        = $logData['user_id'] ?? null;
            $update_type_id = $logData['update_type_id'] ?? null;
            $old_value      = $logData['old_value'] ?? null;
            $new_value      = $logData['new_value'] ?? null;
            $change_date    = $logData['change_date'] ?? null;

            if ( $site_id && $entry_id && $field_id ) {
                
            }

        }

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'entry_id' => $item->entry_id,
            'site_id' => $item->site_id,
            'field_id' => $item->field_id,
            'user_id' => $item->user_id,
            'update_type_id' => $item->update_type_id,
            'old_value' => $item->old_value,
            'new_value' => $item->new_value,
            'change_date' => $item->change_date,
            'Model' => $item
        ];
        return $res;
    }

}