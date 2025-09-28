<?php

namespace App\Http\Controllers\User;

use App\Actions\CreateTransaction;
use App\Http\Controllers\Controller;
use App\Jobs\ExportTransactionsJob;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    //
    public function index(Request $request)
    {
        try{

            $transactions = $request->user()->wallet
                                    ->transactions()
                                    ->latest()
                                    ->paginate(10);
        
            return response()->json([
                'transactions' => $transactions
            ], 200);


        } catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function store(Request $request, CreateTransaction $createTransaction)
    {
        try{

            $validated = $request->validate([
                'entry' => 'required|in:credit,debit',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
            ]);

            $transaction = $createTransaction->execute(
                wallet: $request->user()->wallet,
                entry: $validated['entry'],
                amount: $validated['amount'],
                description: $validated['description'] ?? null,
            );
        
            return response()->json([
                'message' => 'Transaction created successfully.',
                'transaction' => $transaction,
            ], 201);


        } catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }

    public function export(Request $request)
    {
        try{

            $user = $request->user();

            ExportTransactionsJob::dispatch($user);
        
            return response()->json([
                'message' => 'Your export is being processed. You will be notified once it’s ready.'
            ], 202);


        } catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }
}
