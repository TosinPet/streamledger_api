<?php
namespace App\Actions;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Junges\Kafka\Facades\Kafka;

class CreateTransaction
{
    public function execute(Wallet $wallet, string $entry, float $amount, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($wallet, $entry, $amount, $description) {
            // Validate debit
            if ($entry === 'debit' && $wallet->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient funds.'],
                ]);
            }

            // Update wallet balance
            $newBalance = $entry === 'credit'
                ? $wallet->balance + $amount
                : $wallet->balance - $amount;

            $wallet->update(['balance' => $newBalance]);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'entry' => $entry,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'reference' => uniqid('txn_'), // better: use UUID
                'description' => $description,
            ]);

            // $payload = [
            //     'user_id'   => $wallet->user_id,
            //     'entry'     => $transaction->entry,
            //     'amount'    => $transaction->amount,
            //     'balance'   => $transaction->balance,
            //     'timestamp' => Carbon::now()->toISOString(),
            // ];

            // Kafka::publish()
            //     ->onTopic('transactions')
            //     ->withBodyKey('transaction', $payload)
            //     ->send();

            return $transaction;
        });
    }
}

