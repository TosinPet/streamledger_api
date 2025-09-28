<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionsExport implements FromView
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function view(): View
    {
        $transactions = Transaction::where('user_id', $this->user->id)->get();

        return view('exports.transactions', [
            'transactions' => $transactions
        ]);
    }
}