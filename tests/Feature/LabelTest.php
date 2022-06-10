<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LabelTest extends TestCase
{
    /** @test */
    public function user_can_list_all_labels()
    {
        $this->actingAs($this->user)
            ->getJson('api/labels')
            ->assertOk();
    }

    /** @test */
    public function creating_label_should_return_error_without_data()
    {
        $attributes = [];

        $this->actingAs($this->user)
            ->postJson('api/labels', $attributes)
            ->assertStatus(422);
    }

    /** @test */
    public function user_can_create_labels()
    {
        $attributes = [
            'title' => 'Label one',
        ];

        $this->actingAs($this->user)
            ->postJson('api/labels', $attributes)
            ->assertStatus(201);
    }

    /** @test */
    public function labels_should_be_unique()
    {
        $attributes = [
            'title' => 'Label one',
        ];

        $this->actingAs($this->user)
            ->postJson('api/labels', $attributes)
            ->assertStatus(201);

        $this->actingAs($this->user)
            ->postJson('api/labels', $attributes)
            ->assertStatus(422);
    }
}
