<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\DeliberationThread;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\Participant;
use App\Models\Cds\DeliberationMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliberationThreadTest extends TestCase
{
    use RefreshDatabase;

    public function test_deliberation_thread_can_be_created(): void
    {
        $issue = DecisionIssue::factory()->create();
        $thread = DeliberationThread::factory()->create(['issue_id' => $issue->id]);

        $this->assertDatabaseHas('deliberation_threads', ['id' => $thread->id]);
        $this->assertEquals($issue->id, $thread->issue_id);
    }

    public function test_deliberation_thread_belongs_to_issue(): void
    {
        $issue = DecisionIssue::factory()->create();
        $thread = DeliberationThread::factory()->create(['issue_id' => $issue->id]);

        $this->assertInstanceOf(DecisionIssue::class, $thread->issue);
    }

    public function test_deliberation_thread_belongs_to_creator(): void
    {
        $creator = Participant::factory()->create();
        $thread = DeliberationThread::factory()->create(['created_by_id' => $creator->id]);

        $this->assertInstanceOf(Participant::class, $thread->createdBy);
    }

    public function test_deliberation_thread_can_have_messages(): void
    {
        $thread = DeliberationThread::factory()->create();
        DeliberationMessage::factory()->create(['thread_id' => $thread->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $thread->messages);
        $this->assertEquals(1, $thread->messages->count());
    }

    public function test_deliberation_thread_increments_message_count(): void
    {
        $thread = DeliberationThread::factory()->create(['message_count' => 0]);
        $thread->incrementMessageCount();

        $this->assertEquals(1, $thread->fresh()->message_count);
    }

    public function test_deliberation_thread_status_methods(): void
    {
        $thread = new DeliberationThread(['status' => 'open']);
        $this->assertTrue($thread->isOpen());

        $thread->status = 'closed';
        $this->assertTrue($thread->isClosed());

        $thread->status = 'archived';
        $this->assertTrue($thread->isArchived());
    }
}
