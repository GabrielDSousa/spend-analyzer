<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteTransactionRequest;
use App\Http\Requests\ShowTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\MapResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;

class TransactionController extends Controller
{
    private const EXCEPTIONS_MESSAGE_UNAUTHORIZED = 'exceptions.message.unauthorized';

    /**
     * Display a listing of the resource.
     *
     * @param ShowTransactionRequest $request
     * @return MapResource
     */
    public function index(ShowTransactionRequest $request)
    {
        return $request->user()->transactions()->latest()
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
        $transaction = $request->user()->transactions()->create([
            'date' => $request->get('date'),
            'amount' => $request->get('amount'),
            'description' => $request->get('description'),
            'file' => $request->get('file'),
            'type' => $request->get('type'),
            'bank' => $request->get('bank')
        ]);

        return response()->json(["transaction" => $transaction], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param ShowTransactionRequest $request
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function show(ShowTransactionRequest $request, Transaction $transaction)
    {
        if($request->user()->id === $transaction->user()->first()->id) {
            return response()->json(["transaction" => $transaction], 200);
        }

        throw new UnauthorizedException(config(self::EXCEPTIONS_MESSAGE_UNAUTHORIZED));
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
        if($request->user()->id === $transaction->user()->first()->id) {
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

        throw new UnauthorizedException(config(self::EXCEPTIONS_MESSAGE_UNAUTHORIZED));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteTransactionRequest $request
     * @param Transaction $transaction
     * @return JsonResponse
     */
    public function destroy(DeleteTransactionRequest $request, Transaction $transaction)
    {
        if($request->user()->id === $transaction->user()->first()->id) {
            $transaction->destroy($transaction->id);
            return response()->json(["success"], 200);
        }

        throw new UnauthorizedException(config(self::EXCEPTIONS_MESSAGE_UNAUTHORIZED));
    }
}
