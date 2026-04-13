<?php

namespace App\Http\Actions\Api\Data;

use App\Http\Actions\Api\ActionsApiAbstract;
use App\Services\Data\DataEntriesService;

class DataActions extends ActionsApiAbstract
{
    protected DataEntriesService $entriesService;

    public function __construct()
    {
        $this->entriesService = new DataEntriesService();
    }

    public function listEntries($request, int $site_id)
    {
        $filters = [
            'entry_id' => $request->input('entry_id'),
        ];

        $sorting = [
            'by'  => $request->input('sort_by', 'entry_id'),
            'dir' => $request->input('sort_dir', 'desc'),
        ];

        $paginate = (int) $request->input('per_page', 25);
        $pageNum  = (int) $request->input('page', 1);

        $res = $this->entriesService->listEntries($site_id, $filters, $sorting, $paginate, $pageNum);

        return $this->returnSuccess('Entries retrieved successfully', $res);
    }

    public function entryUpdates(int $site_id, int $entry_id)
    {
        $res = $this->entriesService->getEntryUpdates($site_id, $entry_id);
        return $this->returnSuccess('Entry updates retrieved successfully', ['items' => $res]);
    }

    public function entryEmails(int $site_id, int $entry_id)
    {
        $res = $this->entriesService->getEntryEmails($site_id, $entry_id);
        return $this->returnSuccess('Entry emails retrieved successfully', ['items' => $res]);
    }
}
