<?php

namespace Tests\Feature\Cds;

use App\Models\User;
use App\Models\Cds\Participant;
use App\Models\Cds\Submission;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\ValidationEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CdsValidationAndFramingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_issues_index_is_accessible(): void
    {
        $this->actingAsUser();

        $response = $this->get('/cds/issues');
        $response->assertStatus(200);
    }

    public function test_issue_can_be_created_from_submission(): void
    {
        $user = $this->actingAsUser();
        $participant = Participant::factory()->create(['user_id' => $user->id]);
        $submission = Submission::factory()->submitted()->create(['submitter_id' => $participant->id]);

        $response = $this->post('/cds/issues', [
            'submission_id' => $submission->id,
            'framed_problem' => 'How to allocate resources effectively?',
            'scope' => 'Regional resource allocation',
            'success_criteria' => 'All regions have fair access',
            'constraints' => 'Budget limitations',
            'priority' => 5,
            'decision_type' => 'resource_allocation',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('decision_issues', ['framed_problem' => 'How to allocate resources effectively?']);
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'status' => 'framed']);
    }

    public function test_issue_can_be_viewed(): void
    {
        $this->actingAsUser();
        $issue = DecisionIssue::factory()->create();

        $response = $this->get("/cds/issues/{$issue->id}");
        $response->assertStatus(200);
    }

    public function test_issue_can_be_framed(): void
    {
        $this->actingAsUser();
        $issue = DecisionIssue::factory()->draft()->create();

        $response = $this->post("/cds/issues/{$issue->id}/frame");

        $response->assertRedirect();
        $this->assertDatabaseHas('decision_issues', ['id' => $issue->id, 'status' => 'framing']);
    }

    public function test_issue_status_can_be_updated(): void
    {
        $this->actingAsUser();
        $issue = DecisionIssue::factory()->create(['status' => 'draft']);

        $response = $this->post("/cds/issues/{$issue->id}/status", [
            'status' => 'framing',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('decision_issues', ['id' => $issue->id, 'status' => 'framing']);
    }

    public function test_deliberation_can_be_started(): void
    {
        $this->actingAsUser();
        $issue = DecisionIssue::factory()->create(['status' => 'framing']);

        $response = $this->post("/cds/issues/{$issue->id}/deliberation");

        $response->assertRedirect();
        $this->assertDatabaseHas('decision_issues', ['id' => $issue->id, 'status' => 'deliberation']);
    }

    public function test_validation_event_can_be_created(): void
    {
        $user = $this->actingAsUser();
        $validator = Participant::factory()->create(['user_id' => $user->id]);
        $submission = Submission::factory()->create();

        $event = ValidationEvent::factory()->create([
            'submission_id' => $submission->id,
            'validator_id' => $validator->id,
            'result' => 'valid',
        ]);

        $this->assertDatabaseHas('validation_events', ['id' => $event->id]);
        $this->assertTrue($event->isValid());
    }
}
