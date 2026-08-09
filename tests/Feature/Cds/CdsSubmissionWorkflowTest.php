<?php

namespace Tests\Feature\Cds;

use App\Models\User;
use App\Models\Cds\Participant;
use App\Models\Cds\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CdsSubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedParticipant();
    }

    private function seedParticipant(): void
    {
        $user = User::factory()->create();
        Participant::factory()->create(['user_id' => $user->id]);
    }

    public function test_cds_dashboard_is_accessible(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/cds');
        $response->assertStatus(200);
    }

    public function test_submissions_index_is_accessible(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/cds/submissions');
        $response->assertStatus(200);
    }

    public function test_submission_create_page_is_accessible(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/cds/submissions/create');
        $response->assertStatus(200);
    }

    public function test_submission_can_be_stored(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/cds/submissions', [
            'title' => 'Test Proposal',
            'description' => 'This is a test proposal description.',
            'content' => 'Detailed content here.',
            'category' => 'policy',
            'priority' => 'high',
            'scope' => 'regional',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', ['title' => 'Test Proposal']);
    }

    public function test_submission_can_be_viewed(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $submission = Submission::factory()->create();

        $response = $this->get("/cds/submissions/{$submission->id}");
        $response->assertStatus(200);
    }

    public function test_submission_can_be_updated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $submission = Submission::factory()->create([
            'submitter_id' => Participant::factory()->create(['user_id' => $user->id])->id,
        ]);

        $response = $this->patch("/cds/submissions/{$submission->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description.',
            'category' => 'resource',
            'priority' => 'normal',
            'scope' => 'local',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'title' => 'Updated Title']);
    }

    public function test_submission_can_be_submitted_for_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $submission = Submission::factory()->draft()->create([
            'submitter_id' => Participant::factory()->create(['user_id' => $user->id])->id,
        ]);

        $response = $this->post("/cds/submissions/{$submission->id}/submit");

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'status' => 'submitted']);
    }

    public function test_user_can_delete_own_submission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $participant = Participant::factory()->create(['user_id' => $user->id]);
        $submission = Submission::factory()->create(['submitter_id' => $participant->id]);

        $response = $this->delete("/cds/submissions/{$submission->id}");

        $response->assertRedirect('/cds/submissions');
        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);
    }
}
