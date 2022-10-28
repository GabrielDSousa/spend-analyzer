<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test a validation error on signup
     * @test
     * @return void
     */
    public function signup_required_validation_error()
    {
        $response = $this->postJson('/api/auth/signup', []);

        $response->assertJsonValidationErrors([
            "name" => [
                "The name field is required."
            ],
            "email" => [
                "The email field is required."
            ],
            "password" => [
                "The password field is required."
            ],
            "confirmation" => [
                "The confirmation field is required."
            ],
            "rules" => [
                "The rules field is required."
            ]
        ]);
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Test a successful signup
     * @test
     * @return void
     */
    public function signup_success()
    {
        $response = $this->postJson('/api/auth/signup',
        [
            "name" => fake()->name,
            "email" => fake()->email,
            "password" => "password",
            "confirmation" => "password",
            "rules" => [
                "transaction:create",
                "transaction:read",
                "transaction:update",
                "transaction:delete",
                "map:create",
                "map:read",
                "map:delete"
            ]
        ]);

        $response->assertCreated();
        $response->assertSee('token');
        $this->assertAuthenticated();
        $this->assertCount(1, auth()->user()->tokens()->where('name', auth()->user()->email)->get());
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test a unauthorized login
     * @test
     * @return void
     */
    public function login_required_validation_error()
    {
        //action
        $response = $this->postJson('/api/auth/login',[]);

        //assertion
        $response->assertUnprocessable();
        $response->assertExactJson(
            [
                "message" => "The email field is required. (and 1 more error)",
                "errors" => [
                    "email" => [
                        0 => "The email field is required."
                    ],
                    "password" => [
                        0 => "The password field is required."
                    ]
                ]
            ]
        );
    }

    /**
     * Test a unauthorized login
     * @test
     * @return void
     */
    public function login_unauthorized()
    {
        //action
        $response = $this->postJson('/api/auth/login',
            [
                "email" => fake()->email,
                "password" => fake()->password,
            ]);

        //assertion
        $response->assertUnauthorized();
    }

    /**
     * Test a successful login
     * @test
     * @return void
     */
    public function login_success()
    {
        //preparation
        $user = User::factory()->create();

        //pre assertion
        $this->assertCount(0, $user->tokens()->where('name', $user->email)->get());

        //action
        $response = $this->postJson('/api/auth/login',
            [
                "email" => $user->email,
                "password" => "password",
            ]);

        //assertion
        $response->assertStatus(200);
        $this->assertCount(1, $user->tokens()->where('name', $user->email)->get());
    }

    /**
     * Test a unauthorized login
     * @test
     * @return void
     */
    public function logout_unauthorized()
    {
        //action
        $response = $this->postJson('/api/auth/logout');

        //assertion
        $response->assertUnauthorized();
    }

    /**
     * Test a successful logout
     * @test
     * @return void
     */
    public function logout_success()
    {
        //preparation
        $user = User::factory()->create();
        $token = $user->createToken($user->email, $user->permissions)->plainTextToken;

        //pre assertion
        $this->assertCount(1, $user->tokens()->where('name', $user->email)->get());

        //action
        $response = $this->withToken($token)->postJson('/api/auth/logout');

        //assertion
        $response->assertStatus(200);
        $this->assertCount(0, $user->tokens()->where('name', $user->email)->get());
    }
}
