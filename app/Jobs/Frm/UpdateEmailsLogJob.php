<?php

namespace App\Jobs\Frm;

use App\Services\Frm\FrmEmailLogService;
use App\Services\QueueStatsService;
use Illuminate\Bus\Queueable;
use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateEmailsLogJob implements ShouldQueue
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
            $service = new FrmEmailLogService();
            $service->updateDataMultiple($this->data, $this->site);
        } finally {
            QueueStatsService::decrement((int) ($this->site['id'] ?? 0), QueueStatsService::TYPE_EMAILS);
        }
    }

    public function failed(?Throwable $e = null): void
    {
        QueueStatsService::decrement((int) ($this->site['id'] ?? 0), QueueStatsService::TYPE_EMAILS);
    }
}
