<?php

namespace Tests\Feature;

use App\Enums\ComplexStatus;
use App\Models\Complex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplexManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_can_create_complex()
    {
        $complexData = [
            'name' => 'Тестовый ЖК',
            'address' => 'ул. Тестовая, 1',
            'status' => ComplexStatus::Planning,
            'description' => 'Описание тестового ЖК',
        ];

        $response = $this->post(route('platform.complex.save'), [
            'complex' => $complexData
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('complexes', $complexData);
    }

    public function test_can_update_complex()
    {
        $complex = Complex::factory()->create();

        $updatedData = [
            'name' => 'Обновленное название',
            'status' => 'construction',
        ];

        $response = $this->post(route('platform.complex.save', $complex), [
            'complex' => $updatedData
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('complexes', array_merge(
            ['id' => $complex->id],
            $updatedData
        ));
    }

    public function test_can_delete_complex()
    {
        $complex = Complex::factory()->create();

        $response = $this->post(route('platform.complex.remove', $complex));

        $response->assertRedirect();
        $this->assertSoftDeleted($complex);
    }

    public function test_complex_requires_name_and_address()
    {
        $response = $this->post(route('platform.complex.save'), [
            'complex' => [
                'status' => ComplexStatus::Planning
            ]
        ]);

        $response->assertSessionHasErrors(['complex.name', 'complex.address']);
    }
}
