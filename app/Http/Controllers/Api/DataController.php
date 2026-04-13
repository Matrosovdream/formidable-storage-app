<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Actions\Api\Data\DataActions;

class DataController extends Controller
{
    public function __construct(
        protected DataActions $actions
    ) {
    }

    public function entries(Request $request, int $site_id)
    {
        return $this->actions->listEntries($request, $site_id);
    }

    public function entryUpdates(Request $request, int $site_id, int $entry_id)
    {
        return $this->actions->entryUpdates($site_id, $entry_id);
    }

    public function entryEmails(Request $request, int $site_id, int $entry_id)
    {
        return $this->actions->entryEmails($site_id, $entry_id);
    }
}
