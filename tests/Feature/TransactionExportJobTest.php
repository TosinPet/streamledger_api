<?php

use App\Jobs\ExportTransactionsJob;
use App\Models\User;
use App\Notifications\TransactionExportReady;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

it('sends notification when export job runs', function () {
    Notification::fake();
    Storage::fake('public');
    Excel::fake();

    $user = User::factory()->create();

    // Run the job synchronously instead of dispatching
    (new ExportTransactionsJob($user))->handle();

    Notification::assertSentTo(
        [$user],
        TransactionExportReady::class,
        function ($notification, $channels) {
            expect($notification->fileUrl)->toContain('/storage/exports/transactions_user_');
            return true;
        }
    );
});
