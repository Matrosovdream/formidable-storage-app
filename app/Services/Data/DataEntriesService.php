<?php

namespace App\Services\Data;

use Illuminate\Support\Facades\DB;
use App\Services\Frm\FrmEntryHistoryService;

class DataEntriesService
{
    protected FrmEntryHistoryService $historyService;

    public function __construct()
    {
        $this->historyService = new FrmEntryHistoryService();
    }

    public function listEntries(int $site_id, array $filters = [], array $sorting = [], int $paginate = 25, int $pageNum = 1): array
    {
        $sub = DB::table('frm_entry_history')
            ->select('entry_id', DB::raw('change_date as last_update'))
            ->where('site_id', $site_id)
            ->unionAll(
                DB::table('frm_emails_log')
                    ->select('entry_id', DB::raw('date_sent as last_update'))
                    ->where('site_id', $site_id)
            );

        $query = DB::query()
            ->fromSub($sub, 'e')
            ->select('e.entry_id', DB::raw('MAX(e.last_update) as last_update'))
            ->whereNotNull('e.entry_id')
            ->groupBy('e.entry_id');

        if (!empty($filters['entry_id'])) {
            $query->having('e.entry_id', '=', (int) $filters['entry_id']);
        }

        $sortBy  = $sorting['by']  ?? 'entry_id';
        $sortDir = strtolower($sorting['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $sortBy  = in_array($sortBy, ['entry_id', 'last_update']) ? $sortBy : 'entry_id';

        $query->orderBy($sortBy, $sortDir);

        $paginator = $query->paginate($paginate, ['*'], 'page', $pageNum);

        $rows = collect($paginator->items());
        $entry_ids = $rows->pluck('entry_id')->all();

        $updatesCounts = [];
        $emailsCounts  = [];

        if (!empty($entry_ids)) {
            $updatesCounts = DB::table('frm_entry_history')
                ->where('site_id', $site_id)
                ->whereIn('entry_id', $entry_ids)
                ->select('entry_id', DB::raw('COUNT(*) as c'))
                ->groupBy('entry_id')
                ->pluck('c', 'entry_id')
                ->all();

            $emailsCounts = DB::table('frm_emails_log')
                ->where('site_id', $site_id)
                ->whereIn('entry_id', $entry_ids)
                ->select('entry_id', DB::raw('COUNT(*) as c'))
                ->groupBy('entry_id')
                ->pluck('c', 'entry_id')
                ->all();
        }

        $items = $rows->map(function ($row) use ($updatesCounts, $emailsCounts) {
            $entry_id = (int) $row->entry_id;
            return [
                'entry_id'       => $entry_id,
                'last_update'    => $row->last_update,
                'updates_count'  => (int) ($updatesCounts[$entry_id] ?? 0),
                'emails_count'   => (int) ($emailsCounts[$entry_id] ?? 0),
            ];
        })->values()->all();

        return [
            'items'      => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total_items'  => $paginator->total(),
                'total_pages'  => (int) ceil($paginator->total() / max($paginator->perPage(), 1)),
            ],
            'sorting' => ['by' => $sortBy, 'dir' => $sortDir],
        ];
    }

    public function getEntryUpdates(int $site_id, int $entry_id): array
    {
        $res = $this->historyService->getEntryHistory($entry_id, ['id' => $site_id]);
        $items = is_array($res) ? ($res['value'] ?? $res) : [];
        return is_array($items) ? $items : [];
    }

    public function getEntryEmails(int $site_id, int $entry_id): array
    {
        return DB::table('frm_emails_log')
            ->where('site_id', $site_id)
            ->where('entry_id', $entry_id)
            ->orderBy('date_sent', 'desc')
            ->get()
            ->map(fn($r) => (array) $r)
            ->all();
    }
}
