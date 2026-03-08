<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\DTO\PremiseFilterDTO;
use App\Models\Building;
use App\Models\Complex;
use App\Models\Floor;
use App\Models\Premise;
use App\Models\Section;
use App\Services\PremiseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PremiseServiceTest extends TestCase
{
    use RefreshDatabase;

    private PremiseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PremiseService::class);
    }

    public function test_can_get_filtered_premises(): void
    {
        // Arrange
        $complex = Complex::factory()->create();
        $building = Building::factory()->for($complex)->create();
        $section = Section::factory()->for($building)->create();
        $floor = Floor::factory()->for($section)->create();

        Premise::factory()->for($floor)->count(3)->create(['status' => 'available']);
        Premise::factory()->for($floor)->count(2)->create(['status' => 'sold']);

        $filter = new PremiseFilterDTO(status: 'available');

        // Act
        $result = $this->service->getFilteredPremises($filter);

        // Assert
        $this->assertEquals(3, $result->total());
    }

    public function test_can_get_premise_with_relations(): void
    {
        // Arrange
        $premise = Premise::factory()
            ->withRelations()
            ->create();

        // Act
        $result = $this->service->getPremiseWithRelations($premise->id);

        // Assert
        $this->assertNotNull($result);
        $this->assertTrue($result->relationLoaded('floor'));
        $this->assertTrue($result->relationLoaded('images'));
        $this->assertTrue($result->relationLoaded('statusHistory'));
    }

    public function test_can_create_premise(): void
    {
        // Arrange
        $floor = Floor::factory()->create();
        $data = [
            'floor_id' => $floor->id,
            'number' => '101',
            'type' => 'apartment',
            'rooms' => 2,
            'total_area' => 65.5,
            'status' => 'available',
            'base_price' => 5000000,
            'floor' => 1,
        ];

        // Act
        $premise = $this->service->createPremise($data);

        // Assert
        $this->assertDatabaseHas('premises', ['number' => '101']);
        $this->assertEquals('apartment', $premise->type);
    }

    public function test_can_update_premise(): void
    {
        // Arrange
        $premise = Premise::factory()->create(['base_price' => 5000000]);
        $updateData = ['base_price' => 5500000];

        // Act
        $updated = $this->service->updatePremise($premise, $updateData);

        // Assert
        $this->assertEquals(5500000, $updated->base_price);
        $this->assertDatabaseHas('premises', [
            'id' => $premise->id,
            'base_price' => 5500000,
        ]);
    }

    public function test_can_delete_premise(): void
    {
        // Arrange
        $premise = Premise::factory()->create();

        // Act
        $result = $this->service->deletePremise($premise);

        // Assert
        $this->assertTrue($result);
        $this->assertSoftDeleted($premise);
    }

    public function test_can_get_statistics(): void
    {
        // Arrange
        Premise::factory()->count(5)->create(['status' => 'available']);
        Premise::factory()->count(3)->create(['status' => 'sold']);
        Premise::factory()->count(2)->create(['type' => 'studio']);

        // Act
        $stats = $this->service->getStatistics();

        // Assert
        $this->assertEquals(10, $stats['total']);
        $this->assertArrayHasKey('by_status', $stats);
        $this->assertArrayHasKey('by_type', $stats);
        $this->assertArrayHasKey('average_price', $stats);
    }
}
