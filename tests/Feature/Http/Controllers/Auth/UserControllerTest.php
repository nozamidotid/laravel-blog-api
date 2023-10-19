<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/v1/register', [
            "name" => "fardan",
            "username" => "fardan",
            "password" => "password"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "fardan",
                    "username" => "fardan",
                ],
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/v1/register', [
            "name" => "",
            "username" => "fardan",
            "password" => "password"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ]
                ],
            ]);
    }

    public function testRegisterUserAlreadyRegistered()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/v1/register', [
            "name" => "fardan",
            "username" => "fardan",
            "password" => "password"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username has already been taken."
                    ]
                ],
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post("/api/v1/login", [
            "username" => "fardan",
            "password" => "password"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "fardan",
                    "username" => "fardan",
                ]
            ]);
    }

    public function testLoginUserNotFound()
    {
        $this->seed([UserSeeder::class]);

        $this->post("/api/v1/login", [
            "username" => "tidakada",
            "password" => "password"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong!"
                    ]
                ]
            ]);
    }

    public function testLoginWrongPassword()
    {
        $this->seed([UserSeeder::class]);

        $this->post("/api/v1/login", [
            "username" => "fardan",
            "password" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong!"
                    ]
                ]
            ]);
    }

    public function testGetUserSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get("/api/v1/user", [
            "api_key" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "fardan",
                    "username" => "fardan",
                    "token" => "token"

                ]
            ]);
    }

    public function testGetUserUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->get("/api/v1/user")
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    public function testGetUserInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        $this->get("/api/v1/user", [
            "api_key" => "salah"
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
}
