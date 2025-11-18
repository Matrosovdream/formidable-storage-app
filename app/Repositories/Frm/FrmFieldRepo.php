<?php
namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmField;


class FrmFieldRepo extends AbstractRepo
{

    protected $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmField;

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'field_id' => $item->field_id,
            'site_id' => $item->site_id,
            'key' => $item->key,
            'type' => $item->type,
            'label' => $item->label,
            'Model' => $item
        ];
        return $res;
    }

}