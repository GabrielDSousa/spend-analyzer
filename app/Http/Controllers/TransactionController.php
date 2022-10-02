<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return TransactionResource
     */
    public function index()
    {
        return Transaction::latest()
            ->filter(request(['date', 'description', 'file', 'type', 'bank']))
            ->paginate(18)
            ->withQueryString();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTransactionRequest $request
     * @return JsonResponse
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = (new Transaction)->create([
            'date' => $request->get('date'),
            'amount' => $request->get('amount'),
            'description' => $request->get('description'),
            'file' => $request->get('file'),
            'type' => $request->get('type'),
            'bank' => $request->get('bank'),
        ]);

        return response()->json(["transaction" => $transaction], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function show(Transaction $transaction)
    {
        return response()->json(["transaction" => $transaction], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTransactionRequest $request
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update([
            'date' => $request->get('date') ?? $transaction->date,
            'amount' => $request->get('amount') ?? $transaction->amount,
            'description' => $request->get('description') ?? $transaction->description,
            'file' => $request->get('file') ?? $transaction->file,
            'type' => $request->get('type') ?? $transaction->type,
            'bank' => $request->get('bank') ?? $transaction->bank,
        ]);

        return response()->json(["transaction" => $transaction], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->destroy($transaction->id);
        return response()->json(["success"], 200);
    }
}
