<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{

    /** @test */
    public function for_creating_account_email_should_be_unique()
    {
        $attributes = [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '123456789',
        ];

        $this->postJson('/api/register', $attributes)
            ->assertStatus(201);


        $this->postJson('/api/register', $attributes)
            ->assertStatus(422);
    }

    /** @test */
    public function guest_can_create_account()
    {
        $attributes = [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '123456789',
        ];

        $this->postJson('/api/register', $attributes)
            ->assertStatus(201);
    }


    /** @test */
    public function login_into_account_without_data()
    {
        $this->guest_can_create_account();

        $this->postJson('/api/login', [])
            ->assertStatus(422);
    }

    /** @test */
    public function login_into_account()
    {
        $this->guest_can_create_account();

        $attributes = [
            'email' => 'test@example.com',
            'password' => '123456789',
        ];

        $this->postJson('/api/login', $attributes)
            ->assertStatus(200)
            ->assertJsonStructure([
                'token'
            ]);
    }
}
