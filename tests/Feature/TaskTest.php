<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /** @test */
    public function creating_task_should_return_error_without_data()
    {
        $this->actingAs($this->user)
            ->postJson('/api/tasks', [])
            ->assertStatus(422);
    }

    /** @test */
    public function task_title_and_description_should_be_string_otherwise_return_error()
    {
        $this->createLabelsForTest();

        $attributes = [
            'title' => 2,
            'description' => 3,
            'labels' => [1]
        ];

        $this->actingAs($this->user)->postJson('/api/tasks', $attributes)
            ->assertStatus(422);
    }

    /** @test */
    public function when_creating_task_provided_labels_should_not_duplicated()
    {
        $this->createLabelsForTest('label two');

        $attributes = [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'labels' => [1, 1]
        ];

        $this->actingAs($this->user)->postJson('/api/tasks', $attributes)
            ->assertStatus(422);
    }

    /** @test */
    public function user_can_create_task()
    {
        $this->createLabelsForTest('label three');

        $attributes = [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'labels' => [1]
        ];

        $this->actingAs($this->user)->postJson('/api/tasks', $attributes)
            ->assertStatus(201);
    }

    /** @test */
    public function user_can_list_all_task()
    {
        $this->user_can_create_task();

        $this->actingAs($this->user)
            ->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'labels'
                    ]
                ]
            ]);
    }

    /** @test */
    public function user_can_edit_task()
    {
        $this->user_can_create_task();

        $attributes = [
            'title' => 'Test Title updated',
            'description' => 'Test Description updated',
        ];

        $this->actingAs($this->user)
            ->putJson('/api/tasks/1', $attributes)
            ->assertOk();
    }

    /** @test */
    public function user_can_add_label_to_task()
    {
        $this->user_can_create_task();

        $this->createLabelsForTest('label four');

        $attributes = [
            'labels' => [2]
        ];

        $this->actingAs($this->user)
            ->postJson('api/tasks/1/add-labels', $attributes)
            ->assertOk();
    }

    /** @test */
    public function while_updating_task_status_should_return_when_no_data_passed()
    {
        $this->user_can_create_task();

        $this->putJson('api/tasks/1/update-status', [])->assertStatus(422);
    }


    /** @test */
    public function while_updating_status_should_return_error_on_wrong_data()
    {
        $this->user_can_create_task();

        $attributes = [
            'status' => 'something',
        ];

        $this->putJson('api/tasks/1/update-status', $attributes)->assertStatus(422);
    }

    /** @test */
    public function user_can_update_task_status_to_close()
    {
        $this->user_can_create_task();

        $attributes = [
            'status' => 'close',
        ];

        $this->putJson('api/tasks/1/update-status', $attributes)
            ->assertOk();
    }

    /** @test */
    public function user_can_see_task_detail()
    {
        $this->user_can_create_task();

        $this->getJson('api/tasks/1')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'labels',
                ]
            ]);;
    }

    public function createLabelsForTest($title = 'label one')
    {
        $this->actingAs($this->user)
            ->postJson('/api/labels', ['title' => $title])
            ->assertStatus(201);
    }
}
