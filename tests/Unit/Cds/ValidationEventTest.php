<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\Participant;
use App\Models\Cds\ValidationEvent;
use App\Models\Cds\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_event_can_be_created(): void
    {
        $submission = Submission::factory()->create();
        $validator = Participant::factory()->create();
        $event = ValidationEvent::factory()->create([
            'submission_id' => $submission->id,
            'validator_id' => $validator->id,
        ]);

        $this->assertDatabaseHas('validation_events', ['id' => $event->id]);
        $this->assertEquals($submission->id, $event->submission_id);
    }

    public function test_validation_event_belongs_to_submission(): void
    {
        $submission = Submission::factory()->create();
        $event = ValidationEvent::factory()->create(['submission_id' => $submission->id]);

        $this->assertInstanceOf(Submission::class, $event->proposal);
    }

    public function test_validation_event_belongs_to_validator(): void
    {
        $validator = Participant::factory()->create();
        $event = ValidationEvent::factory()->create(['validator_id' => $validator->id]);

        $this->assertInstanceOf(Participant::class, $event->validator);
    }

    public function test_validation_event_status_methods(): void
    {
        $event = new ValidationEvent(['result' => 'valid']);
        $this->assertTrue($event->isValid());
        $this->assertFalse($event->isInvalid());

        $event->result = 'invalid';
        $this->assertTrue($event->isInvalid());

        $event->result = 'needs_revision';
        $this->assertTrue($event->needsRevision());

        $event->result = 'pending';
        $this->assertTrue($event->isPending());
    }

    public function test_validation_event_can_be_blocking(): void
    {
        $event = ValidationEvent::factory()->blocking()->create();
        $this->assertTrue($event->is_blocking);
    }

    public function test_validation_event_can_supersede_another(): void
    {
        $original = ValidationEvent::factory()->create();
        $new = ValidationEvent::factory()->create(['supersedes_id' => $original->id]);

        $this->assertEquals($original->id, $new->supersedes_id);
        $this->assertInstanceOf(ValidationEvent::class, $new->supersedes);
    }
}
