<?php
namespace App\Repositories\Frm;

use App\Repositories\AbstractRepo;
use App\Models\Frm\FrmField;
use Illuminate\Support\Facades\DB;


class FrmFieldRepo extends AbstractRepo
{

    public $model;

    protected $fields = [];

    protected int $insertChunkSize = 200;

    public function __construct()
    {
        $this->model = new FrmField;

    }

    public function updateFieldsMultiple( array $data, array $site ): bool{

        $site_id = $site['id'] ?? null;
        $fields  = $data['fields'] ?? [];

        if ( ! $site_id || empty( $fields ) ) {
            return true;
        }

        $now  = now();
        $rows = [];
        foreach ( $fields as $fieldData ) {
            $field_id = $fieldData['field_id'] ?? null;
            if ( ! $field_id ) {
                continue;
            }
            $rows[] = [
                'site_id'    => $site_id,
                'field_id'   => $field_id,
                'key'        => $fieldData['field_key'] ?? null,
                'type'       => $fieldData['type'] ?? null,
                'label'      => $fieldData['label'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ( empty( $rows ) ) {
            return true;
        }

        DB::transaction( function () use ( $rows ) {
            foreach ( array_chunk( $rows, $this->insertChunkSize ) as $chunk ) {
                $this->model->insert( $chunk );
            }
        } );

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