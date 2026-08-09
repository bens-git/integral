<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\Submission;
use App\Models\Cds\Participant;
use App\Models\Cds\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_can_be_created(): void
    {
        $participant = Participant::factory()->create();
        $submission = Submission::factory()->create([
            'submitter_id' => $participant->id,
        ]);

        $this->assertDatabaseHas('submissions', ['id' => $submission->id]);
        $this->assertEquals($participant->id, $submission->submitter_id);
    }

    public function test_submission_has_uuid_primary_key(): void
    {
        $submission = Submission::factory()->create();
        $this->assertNotEmpty($submission->id);
        $this->assertIsString($submission->id);
    }

    public function test_proposal_extends_submission(): void
    {
        $participant = Participant::factory()->create();
        $proposal = Proposal::factory()->create([
            'submitter_id' => $participant->id,
        ]);

        $this->assertInstanceOf(Submission::class, $proposal);
        $this->assertDatabaseHas('submissions', ['id' => $proposal->id]);
    }

    public function test_submission_belongs_to_participant(): void
    {
        $participant = Participant::factory()->create();
        $submission = Submission::factory()->create(['submitter_id' => $participant->id]);

        $this->assertInstanceOf(Participant::class, $submission->submitter);
        $this->assertEquals($participant->id, $submission->submitter->id);
    }

    public function test_submission_status_methods(): void
    {
        $submission = new Submission(['status' => 'draft']);
        $this->assertTrue($submission->isDraft());
        $this->assertFalse($submission->isSubmitted());

        $submission->status = 'submitted';
        $this->assertTrue($submission->isSubmitted());

        $submission->status = 'accepted';
        $this->assertTrue($submission->isAccepted());

        $submission->status = 'rejected';
        $this->assertTrue($submission->isRejected());

        $submission->status = 'implemented';
        $this->assertTrue($submission->isImplemented());
    }

    public function test_submission_fillable_attributes(): void
    {
        $submission = new Submission();
        $fillable = $submission->getFillable();

        $this->assertContains('submitter_id', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('category', $fillable);
        $this->assertContains('priority', $fillable);
        $this->assertContains('scope', $fillable);
    }
}
