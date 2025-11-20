<?php
namespace App\Repositories\Site;

use App\Repositories\AbstractRepo;
use App\Models\Site\SiteToken;


class SiteTokenRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new SiteToken();

    }

    public function mapItem($item)  
    {
        if (empty($item)) {
            return null;
        }

        $res = [
            'id' => $item->id,
            'site_id' => $item->site_id,
            'token' => $item->token,
            'valid_until' => $item->valid_until,
            'Model' => $item
        ];
        return $res;
    }

}