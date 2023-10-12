<?php

namespace Tests\Feature\Http\Controllers\AUth;

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
}
