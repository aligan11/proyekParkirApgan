<?php

namespace App\Jobs;

use App\Models\Topup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeletePendingTopup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $topupId;

    /**
     * Create a new job instance.
     */
    public function __construct($topupId)
    {
        $this->topupId = $topupId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $topup = Topup::find($this->topupId);

        if (!$topup) {
            Log::warning("Topup ID {$this->topupId} tidak ditemukan.");
            return;
        }

        $status = strtolower(trim($topup->status));

        Log::info("Cek Topup ID {$this->topupId} dengan status: {$status}");

        if ($status === 'pending') {
            $topup->delete();
            Log::info("Topup ID {$this->topupId} berhasil dihapus karena status masih pending.");
        } else {
            Log::info("Topup ID {$this->topupId} tidak dihapus karena status bukan pending.");
        }
    }
}
