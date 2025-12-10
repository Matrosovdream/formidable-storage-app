<?php
namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmField;


class FrmFieldRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    public function __construct()
    {
        $this->model = new FrmField;

    }

    public function updateFieldsMultiple( array $data, array $site ): bool{
        
        $site_id = $site['id'];
        $fields = $data['fields'] ?? [];

        foreach ( $fields as $fieldData ) {
            $field_id = $fieldData['field_id'] ?? null;
            $type     = $fieldData['type'] ?? null;
            $key      = $fieldData['field_key'] ?? null;
            $label    = $fieldData['label'] ?? null;

            if ( $site_id && $field_id ) {
                
                $this->model->updateOrCreate(
                    [
                        'site_id'  => $site_id,
                        'field_id' => $field_id,
                    ],
                    [
                        'key'    => $key,
                        'type'   => $type,
                        'label'  => $label
                    ]
                );
            }
        }

        return true;

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