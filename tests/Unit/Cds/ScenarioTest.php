<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\Scenario;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScenarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_scenario_can_be_created(): void
    {
        $issue = DecisionIssue::factory()->create();
        $scenario = Scenario::factory()->create(['issue_id' => $issue->id]);

        $this->assertDatabaseHas('scenarios', ['id' => $scenario->id]);
        $this->assertEquals($issue->id, $scenario->issue_id);
    }

    public function test_scenario_belongs_to_issue(): void
    {
        $issue = DecisionIssue::factory()->create();
        $scenario = Scenario::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(DecisionIssue::class, $scenario->issue);
    }

    public function test_scenario_belongs_to_creator(): void
    {
        $creator = Participant::factory()->create();
        $scenario = Scenario::factory()->create(['created_by_id' => $creator->id]);

        $this->assertInstanceOf(Participant::class, $scenario->createdBy);
    }

    public function test_scenario_can_be_based_on_another(): void
    {
        $parent = Scenario::factory()->create();
        $child = Scenario::factory()->create(['based_on_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->based_on_id);
        $this->assertInstanceOf(Scenario::class, $child->basedOn);
    }

    public function test_scenario_status_methods(): void
    {
        $scenario = new Scenario(['status' => 'draft']);
        $this->assertTrue($scenario->isDraft());

        $scenario->status = 'modeling';
        $this->assertTrue($scenario->isModeling());

        $scenario->status = 'complete';
        $this->assertTrue($scenario->isComplete());
    }

    public function test_scenario_scores_are_cast_to_decimals(): void
    {
        $scenario = Scenario::factory()->create([
            'viability_score' => 85.50,
            'risk_score' => 25.30,
            'impact_score' => 92.10,
        ]);

        $this->assertEquals(85.50, $scenario->viability_score);
        $this->assertEquals(25.30, $scenario->risk_score);
        $this->assertEquals(92.10, $scenario->impact_score);
    }
}
