<?php

namespace App\Jobs\Frm;

use App\Services\Frm\FrmFieldService;
use App\Services\QueueStatsService;
use Illuminate\Bus\Queueable;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateFrmFieldsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $data;
    protected array $site;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, array $site)
    {
        $this->data = $data;
        $this->site = $site;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $service = new FrmFieldService();
            $service->updateFieldsAll($this->data, $this->site);
        } finally {
            QueueStatsService::decrement((int) ($this->site['id'] ?? 0), QueueStatsService::TYPE_FIELDS);
        }
    }

    public function failed(?Throwable $e = null): void
    {
        QueueStatsService::decrement((int) ($this->site['id'] ?? 0), QueueStatsService::TYPE_FIELDS);
    }
}
