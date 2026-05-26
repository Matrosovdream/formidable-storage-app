<?php

namespace App\Services\Data;

use Illuminate\Support\Facades\DB;

class DataEmailsService
{
    protected array $sortable = ['id', 'date_sent', 'status', 'subject', 'email_from', 'email_to', 'created_at'];
    protected array $likeFields = ['subject', 'email_from', 'email_to'];

    public function listEmails(int $site_id, array $filters = [], array $sorting = [], int $paginate = 25, int $pageNum = 1): array
    {
        $query = DB::table('frm_emails_log')->where('site_id', $site_id);

        if (!empty($filters['entry_id'])) {
            $query->where('entry_id', (int) $filters['entry_id']);
        }
        if (!empty($filters['status']) || $filters['status'] === '0' || $filters['status'] === 0) {
            $query->where('status', (int) $filters['status']);
        }
        if (!empty($filters['mailer'])) {
            $query->where('mailer', $filters['mailer']);
        }
        foreach ($this->likeFields as $f) {
            if (!empty($filters[$f])) {
                $query->where($f, 'like', '%' . $filters[$f] . '%');
            }
        }

        $sortBy  = in_array($sorting['by'] ?? null, $this->sortable, true) ? $sorting['by'] : 'date_sent';
        $sortDir = strtolower($sorting['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

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
