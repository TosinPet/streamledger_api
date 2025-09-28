<?php

namespace App\Jobs;

use App\Exports\TransactionsExport;
use App\Models\User;
use App\Notifications\TransactionExportReady;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ExportTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $filePath = 'exports/transactions_user_'.$this->user->id.'.xlsx';

        // Store Excel export locally
        Excel::store(new TransactionsExport($this->user), $filePath, 'public');

        $fileUrl = config('app.url') . '/storage/' . $filePath;
        Log::info(['fileUrl' => $fileUrl]);
        error_log("Export file URL: " . $fileUrl);
        dump($fileUrl);

        // Update DB (optional)
        $this->user->update(['last_export_path' => $filePath]);

        // Notify user by email
        $this->user->notify(new TransactionExportReady($fileUrl));
    }
}
