<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
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

    public function testCreateCategorySuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/v1/category',
            [
                "name" => "laravel BLog API"
            ],
            [
                "api_key" => "token"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "Laravel Blog Api",
                    "slug" => "laravel-blog-api",
                ]
            ]);
    }

    public function testCreateCategoryFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/v1/category',
            [],
            [
                "api_key" => "token"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testCreateCategoryNameAlreadyExist()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $this->post(
            '/api/v1/category',
            [
                "name" => "laravel"
            ],
            [
                "api_key" => "token"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        'The name has already been taken.'
                    ]
                ]
            ]);
    }

    public function testCreateCategoryUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/v1/category',
            [
                "name" => "laravel"
            ],
            [
                "api_key" => "salah"
            ]
        )->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetCategoryByIdSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $category = Category::query()->first();

        $this->get('/api/v1/category/' . $category->id, [
            'api_key' => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "Laravel",
                    "slug" => "laravel"
                ]
            ]);
    }

    public function testGetCategoryByIdNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $category = Category::query()->first();

        $this->get('/api/v1/category/' . $category->id - 1, [
            'api_key' => "token"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["not found!"]
                ]
            ]);
    }

    public function testGetCategoryByIdUnauthorized()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $category = Category::query()->first();

        $this->get('/api/v1/category/', [
            'api_key' => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["unauthorized"]
                ]
            ]);
    }

    public function testUpdateCategorySuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $category = Category::query()->first();

        $this->patch('/api/v1/category/' . $category->id, [
            "name" => "update"
        ],
        [
            'api_key' => "token"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "Update",
                    "slug" => "update",
                ]
            ]);
    }

    public function testUpdateCategoryUnauthorized()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $category = Category::query()->first();

        $this->patch('/api/v1/category/' . $category->id, [
            "name" => "update"
        ],
        [
            'api_key' => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => ["unauthorized"]
                ]
            ]);
    }

    public function testUpdateCategoryNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);

        $category = Category::query()->first();

        $this->patch('/api/v1/category/' . $category->id - 1, [
            "name" => "update"
        ],
        [
            'api_key' => "token"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => ["not found!"]
                ]
            ]);
    }
}
