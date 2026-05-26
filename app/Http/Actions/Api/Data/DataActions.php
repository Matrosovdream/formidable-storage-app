<?php

namespace App\Http\Actions\Api\Data;

use App\Http\Actions\Api\ActionsApiAbstract;
use App\Services\Data\DataEntriesService;
use App\Services\Data\DataEmailsService;
use App\Services\Data\DataEntryUpdatesService;
use App\Services\Data\DataGeneratorService;

class DataActions extends ActionsApiAbstract
{
    protected DataEntriesService $entriesService;
    protected DataEmailsService $emailsService;
    protected DataEntryUpdatesService $updatesService;
    protected DataGeneratorService $generator;

    public function __construct()
    {
        $this->entriesService = new DataEntriesService();
        $this->emailsService  = new DataEmailsService();
        $this->updatesService = new DataEntryUpdatesService();
        $this->generator      = new DataGeneratorService();
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

        return $this->returnSuccess('Entries retrieved successfully', $this->shapeWithTotal($res));
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

    public function listEmails($request, int $site_id)
    {
        $filters = [
            'entry_id'   => $request->input('entry_id'),
            'subject'    => $request->input('subject'),
            'email_from' => $request->input('email_from'),
            'email_to'   => $request->input('email_to'),
            'status'     => $request->input('status'),
            'mailer'     => $request->input('mailer'),
        ];

        $sorting = [
            'by'  => $request->input('sort_by', 'date_sent'),
            'dir' => $request->input('sort_dir', 'desc'),
        ];

        $paginate = (int) $request->input('per_page', 25);
        $pageNum  = (int) $request->input('page', 1);

        $res = $this->emailsService->listEmails($site_id, $filters, $sorting, $paginate, $pageNum);

        return $this->returnSuccess('Emails retrieved successfully', $this->shapeWithTotal($res));
    }

    public function listEntryUpdates($request, int $site_id)
    {
        $filters = [
            'entry_id' => $request->input('entry_id'),
            'field_id' => $request->input('field_id'),
            'user_id'  => $request->input('user_id'),
        ];

        $sorting = [
            'by'  => $request->input('sort_by', 'change_date'),
            'dir' => $request->input('sort_dir', 'desc'),
        ];

        $paginate = (int) $request->input('per_page', 25);
        $pageNum  = (int) $request->input('page', 1);

        $res = $this->updatesService->listEntryUpdates($site_id, $filters, $sorting, $paginate, $pageNum);

        return $this->returnSuccess('Entry updates retrieved successfully', $this->shapeWithTotal($res));
    }

    public function generateEmails($request, int $site_id)
    {
        $amount = max(1, min(10000, (int) $request->input('amount', 10)));
        $length = max(1, min(100000, (int) $request->input('length', 200)));

        $res = $this->generator->generateEmails($site_id, $amount, $length);
        return $this->returnSuccess('Emails generated', $res);
    }

    public function generateFields($request, int $site_id)
    {
        $amount = max(1, min(10000, (int) $request->input('amount', 10)));
        $res = $this->generator->generateFields($site_id, $amount);
        return $this->returnSuccess('Fields generated', $res);
    }

    public function generateEntryUpdates($request, int $site_id)
    {
        $amount = max(1, min(10000, (int) $request->input('amount', 10)));
        $res = $this->generator->generateEntryUpdates($site_id, $amount);
        return $this->returnSuccess('Entry updates generated', $res);
    }

    protected function shapeWithTotal(array $res): array
    {
        if (isset($res['pagination']) && !isset($res['pagination']['total'])) {
            $res['pagination']['total'] = $res['pagination']['total_items'] ?? 0;
        }
        return $res;
    }
}
