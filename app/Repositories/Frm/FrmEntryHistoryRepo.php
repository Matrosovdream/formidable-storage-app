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
            'update_type_id' => $item->update_type_id,
            'value' => $item->value,
            'change_date' => $item->change_date,
            'Model' => $item
        ];
        return $res;
    }

}