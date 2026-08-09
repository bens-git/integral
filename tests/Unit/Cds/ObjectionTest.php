<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\Objection;
use App\Models\Cds\ConsensusModel;
use App\Models\Cds\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_objection_can_be_created(): void
    {
        $consensus = ConsensusModel::factory()->create();
        $objection = Objection::factory()->create(['consensus_id' => $consensus->id]);

        $this->assertDatabaseHas('objection_map', ['id' => $objection->id]);
        $this->assertEquals($consensus->id, $objection->consensus_id);
    }

    public function test_objection_belongs_to_consensus(): void
    {
        $consensus = ConsensusModel::factory()->create();
        $objection = Objection::factory()->create(['consensus_id' => $consensus->id]);

        $this->assertInstanceOf(ConsensusModel::class, $objection->consensus);
    }

    public function test_objection_belongs_to_participant(): void
    {
        $participant = Participant::factory()->create();
        $objection = Objection::factory()->create(['participant_id' => $participant->id]);

        $this->assertInstanceOf(Participant::class, $objection->participant);
    }

    public function test_objection_status_methods(): void
    {
        $objection = new Objection(['status' => 'open']);
        $this->assertTrue($objection->isOpen());

        $objection->status = 'addressed';
        $this->assertTrue($objection->isAddressed());

        $objection->status = 'resolved';
        $this->assertTrue($objection->isResolved());

        $objection->status = 'withdrawn';
        $this->assertTrue($objection->isWithdrawn());

        $objection->status = 'upheld';
        $this->assertTrue($objection->isUpheld());
    }

    public function test_objection_can_be_blocking(): void
    {
        $objection = Objection::factory()->blocking()->create();
        $this->assertTrue($objection->is_blocking);
        $this->assertTrue($objection->isOpen());
    }

    public function test_objection_revision_types(): void
    {
        $this->markTestIncomplete('Objection model does not have revision_type; DecisionRevisionLink does.');
    }
}
