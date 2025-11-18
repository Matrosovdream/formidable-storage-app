<?php
namespace App\Repositories\Site;

use App\Repositories\AbstractRepo;
use App\Models\Site\Site;


class SiteRepo extends AbstractRepo
{

    protected $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new Site();

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'name' => $item->name,
            'url' => $item->url,
            'Model' => $item
        ];
        return $res;
    }

}