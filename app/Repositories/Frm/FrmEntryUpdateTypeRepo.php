<?php
namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmEntryUpdateType;


class FrmEntryUpdateTypeRepo extends AbstractRepo
{

    protected $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmEntryUpdateType();

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'code' => $item->code,
            'title' => $item->title,
            'Model' => $item
        ];
        return $res;
    }

}