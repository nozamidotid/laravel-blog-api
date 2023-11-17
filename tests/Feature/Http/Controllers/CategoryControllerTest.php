<?php

namespace Tests\Feature\Http\Controllers;

use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    public function testGetCategorySuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $this->get("/api/v1/category", [
            "api_key" => "token"
        ])->assertStatus(200)
        ->assertJson([
            "data" => [
                [
                    "name" => "Laravel",
                    "slug" => "laravel",
                ],
                [
                    "name" => "Nestjs",
                    "slug" => "nestjs",
                ],
            ]
        ]);
    }
    public function testGetCategoryFailed()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $this->get("/api/v1/category", [
            "api_key" => "salah"
        ])->assertStatus(401)
        ->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }
}
