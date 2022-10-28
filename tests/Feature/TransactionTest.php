<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private const API_TRANSACTIONS = '/api/transactions';
    private const TRANSACTIONS_QUANTITY = 10;

    /**
     * A unauthorized transaction creation.
     *
     * @test
     * @return void
     */
    public function create_unauthorized()
    {
        //action
        $response = $this->postJson(self::API_TRANSACTIONS, []);

        //assertion
        $response->assertUnauthorized();
    }

    /**
     * A validation error on transaction creation.
     *
     * @test
     * @return void
     */
    public function create_validation_error()
    {
        //preparation
        $user = User::factory()->create();
        $params = [];

        //action
        $response = $this
            ->withToken($user->createToken($user->email, $user->permissions)->plainTextToken)
            ->postJson(self::API_TRANSACTIONS, $params);

        //assertion
        $response->assertJsonValidationErrors([
            "date"=> [
                "The date field is required."
            ],
              "amount"=> [
                "The amount field is required."
            ],
              "description"=> [
                "The description field is required."
            ],
              "type"=> [
                "The type field is required."
            ]
        ]);
    }

    /**
     * A successful transaction creation.
     *
     * @test
     * @return void
     */
    public function create_success()
    {
        //preparation
        $user = User::factory()->create();
        $params = [
            "date" => fake()->date(),
            "amount" => fake()->randomFloat(2, -10000, 10000),
            "description" => fake()->sentence,
            "file" => null,
            "type" => "credit",
            "bank" => "Bank"
        ];

        //action
        $response = $this
            ->withToken($user->createToken($user->email, $user->permissions)->plainTextToken)
            ->postJson(self::API_TRANSACTIONS, $params);

        //assertion
        $response->assertCreated();
        $this->assertDatabaseHas('transactions', [
            'id' => $response->collect()->get('transaction')['id'],
        ]);
        $response->assertExactJson(
            [
                'transaction' =>
                    [
                        "date" => Carbon::createFromFormat("Y-m-d H", "{$params['date']} 0"),
                        "amount" => $params["amount"],
                        "description" => $params["description"],
                        "file" => null,
                        "type" => "credit",
                        "bank" => "Bank",
                        "user_id" => $user->id,
                        "updated_at" => $response->collect()->get('transaction')['updated_at'],
                        "created_at" => $response->collect()->get('transaction')['created_at'],
                        "id" => 1,
                    ]
            ]
        );
    }

    /**
     * A unauthorized read all transactions of a user.
     *
     * @test
     * @return void
     */
    public function read_all_unauthorized()
    {
        //action
        $response = $this
            ->getJson(self::API_TRANSACTIONS.'/all');

        //assertion
        $response->assertUnauthorized();
    }

    /**
     * A successful read all transactions of a user.
     *
     * @test
     * @return void
     */
    public function read_all_success()
    {
        //preparation
        $user = User::factory()->create();
        Transaction::factory()->count(self::TRANSACTIONS_QUANTITY)->create(['user_id' => $user->id]);

        //action
        $response = $this
            ->withToken($user->createToken($user->email, $user->permissions)->plainTextToken)
            ->getJson(self::API_TRANSACTIONS.'/all');

        //assertion
        $response->assertOk();
        $this->assertCount(self::TRANSACTIONS_QUANTITY, collect($response->collect()->get("data")));
    }

    /**
     * A successful read filtered transactions.
     *
     * @test
     * @return void
     */
    public function read_filter_success()
    {
        //preparation
        $user = User::factory()->create();
        $token = $user->createToken($user->email, $user->permissions)->plainTextToken;
        Transaction::factory()->count(self::TRANSACTIONS_QUANTITY)->create(['user_id' => $user->id]);
        $first = $user->transactions()->first();

        //** ******** Filter - bank ********* */
        //action
        $response = $this
            ->withToken($token)
            ->getJson(self::API_TRANSACTIONS."/all/?bank={$first->bank}");

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($first->toArray());

        //** ******** Filter - description ********* */
        //preparation
        $substr_description = Str::substr($first->description, ($this->count() - ($this->count() - 1)));

        //action
        $response = $this
            ->withToken($token)
            ->getJson(self::API_TRANSACTIONS."/all/?description={$substr_description}");

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($first->toArray());

        //** ******** Filter - start and until ********* */
        //preparation
        $start = Carbon::create($first->date)->subDay()->format('Y-m-d');
        $until = Carbon::create($first->date)->addDay()->format('Y-m-d');

        //action
        $response = $this
            ->withToken($token)
            ->getJson(self::API_TRANSACTIONS."/all/?start={$start}&until={$until}");

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($first->toArray());

        //** ******** Filter - expenses ********* */
        //preparation
        $first->update(['amount' => -1000]);

        //action
        $response = $this
            ->withToken($token)
            ->getJson(self::API_TRANSACTIONS."/all/?expenses=true");

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($first->toArray());

        //** ******** Filter - incomes ********* */
        //preparation
        $first->update(['amount' => 1000]);

        //action
        $response = $this
            ->withToken($token)
            ->getJson(self::API_TRANSACTIONS."/all/?incomes=true");

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($first->toArray());
    }

    /**
     * A successful read one transaction.
     *
     * @test
     * @return void
     */
    public function show_success()
    {
        //preparation
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        //action
        $response = $this
            ->withToken($user->createToken($user->email, $user->permissions)->plainTextToken)
            ->getJson(self::API_TRANSACTIONS."/{$transaction->id}");

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($transaction->toArray());
    }

    /**
     * A successful transaction update.
     *
     * @test
     * @return void
     */
    public function update_success()
    {
        //preparation
        $user = User::factory()->create();
        $transaction = (Transaction::factory()->create(['user_id' => $user->id]))->toArray();
        Arr::pull($transaction, 'updated_at');
        $transaction['date'] = now()->floorWeek()->floorHours();

        //action
        $response = $this
            ->withToken($user->createToken($user->email, $user->permissions)->plainTextToken)
            ->putJson(self::API_TRANSACTIONS."/{$transaction['id']}", ['date' => $transaction['date']->format('Y-m-d')]);

        //assertion
        $response->assertOk();
        $response->assertJsonFragment($transaction);
    }

    /**
     * A successful transaction delete.
     *
     * @test
     * @return void
     */
    public function delete_success()
    {
        //preparation
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        //action
        $response = $this
            ->withToken($user->createToken($user->email, $user->permissions)->plainTextToken)
            ->deleteJson(self::API_TRANSACTIONS."/{$transaction->id}");

        //assertion
        $response->assertOk();
        $this->assertDatabaseMissing('transactions', $transaction->toArray());
    }
}
