<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
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

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::query()->where("username", "fardan")->first();

        $this->patch('/api/v1/user', [
            "name" => "update",
            "password" => "password",
        ], [
            "api_key" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "update"
                ]
            ]);

        $newUser = User::query()->where("username", "fardan")->first();

        $this->assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::query()->where("username", "fardan")->first();

        $this->patch('/api/v1/user', [
            "name" => "fardan",
            "password" => "update",
        ], [
            "api_key" => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "fardan",
                    "username" => "fardan"
                ]
            ]);

        $newUser = User::query()->where("username", "fardan")->first();

        $this->assertNotEquals($oldUser->password, $newUser->password);
    }


    public function testUpdateNameFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/v1/user', [
            "name" => "Lorem ipsum dolor sit amet consectetur adipisicing elit. Dicta qui consequuntur magni neque temporibus corporis adipisci iusto. Facilis enim esse ratione aspernatur eius. Aliquam eos quo molestias vitae saepe corporis.",
            "password" => "password",
        ], [
            "api_key" => "token"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }
}
