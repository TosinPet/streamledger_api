<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Entry</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
        <tr>
            <td>{{ $transaction->id }}</td>
            <td>{{ $transaction->entry }}</td>
            <td>{{ $transaction->amount }}</td>
            <td>{{ $transaction->balance }}</td>
            <td>{{ $transaction->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
