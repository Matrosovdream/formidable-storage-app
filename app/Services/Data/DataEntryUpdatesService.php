<?php

namespace App\Services\Data;

use Illuminate\Support\Facades\DB;

class DataEntryUpdatesService
{
    protected array $sortable = ['id', 'entry_id', 'field_id', 'change_date', 'created_at'];

    public function listEntryUpdates(int $site_id, array $filters = [], array $sorting = [], int $paginate = 25, int $pageNum = 1): array
    {
        $query = DB::table('frm_entry_history as h')
            ->leftJoin('frm_fields as f', function ($join) {
                $join->on('h.field_id', '=', 'f.id');
            })
            ->leftJoin('frm_entry_update_types as t', 't.id', '=', 'h.update_type_id')
            ->where('h.site_id', $site_id)
            ->select(
                'h.id',
                'h.entry_id',
                'h.site_id',
                'h.field_id',
                'h.user_id',
                'h.update_type_id',
                'h.old_value',
                'h.new_value',
                'h.change_date',
                'h.created_at',
                't.code as update_type',
                'f.field_id as field_external_id',
                'f.key as field_key',
                'f.label as field_label',
                'f.type as field_type',
            );

        if (!empty($filters['entry_id'])) {
            $query->where('h.entry_id', (int) $filters['entry_id']);
        }
        if (!empty($filters['field_id'])) {
            // Filter by external field_id (matches frm_fields.field_id)
            $query->where('f.field_id', (int) $filters['field_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('h.user_id', (int) $filters['user_id']);
        }

        $sortBy  = in_array($sorting['by'] ?? null, $this->sortable, true) ? $sorting['by'] : 'change_date';
        $sortDir = strtolower($sorting['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy('h.' . $sortBy, $sortDir);

        $paginator = $query->paginate($paginate, ['*'], 'page', $pageNum);

        $items = collect($paginator->items())->map(fn ($r) => (array) $r)->values()->all();

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'total_items'  => $paginator->total(),
                'total_pages'  => (int) ceil($paginator->total() / max($paginator->perPage(), 1)),
            ],
            'sorting' => ['by' => $sortBy, 'dir' => $sortDir],
        ];
    }
}
