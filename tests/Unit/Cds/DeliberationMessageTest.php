<?php

namespace Tests\Unit\Cds;

use App\Models\Cds\DeliberationMessage;
use App\Models\Cds\DeliberationThread;
use App\Models\Cds\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliberationMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_deliberation_message_can_be_created(): void
    {
        $thread = DeliberationThread::factory()->create();
        $participant = Participant::factory()->create();
        $message = DeliberationMessage::factory()->create([
            'thread_id' => $thread->id,
            'participant_id' => $participant->id,
        ]);

        $this->assertDatabaseHas('deliberation_messages', ['id' => $message->id]);
        $this->assertEquals($thread->id, $message->thread_id);
    }

    public function test_deliberation_message_belongs_to_thread(): void
    {
        $thread = DeliberationThread::factory()->create();
        $message = DeliberationMessage::factory()->create(['thread_id' => $thread->id]);

        $this->assertInstanceOf(DeliberationThread::class, $message->thread);
    }

    public function test_deliberation_message_belongs_to_participant(): void
    {
        $participant = Participant::factory()->create();
        $message = DeliberationMessage::factory()->create(['participant_id' => $participant->id]);

        $this->assertInstanceOf(Participant::class, $message->participant);
    }

    public function test_deliberation_message_can_have_parent(): void
    {
        $parent = DeliberationMessage::factory()->create();
        $child = DeliberationMessage::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertInstanceOf(DeliberationMessage::class, $child->parent);
    }

    public function test_deliberation_message_can_have_children(): void
    {
        $parent = DeliberationMessage::factory()->create();
        DeliberationMessage::factory()->create(['parent_id' => $parent->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $parent->children);
        $this->assertEquals(1, $parent->children->count());
    }

    public function test_deliberation_message_stance_methods(): void
    {
        $message = new DeliberationMessage(['stance' => 'support']);
        $this->assertTrue($message->isSupport());

        $message->stance = 'concern';
        $this->assertTrue($message->isConcern());

        $message->stance = 'objection';
        $this->assertTrue($message->isObjection());

        $message->stance = 'question';
        $this->assertTrue($message->isQuestion());

        $message->stance = 'suggestion';
        $this->assertTrue($message->isSuggestion());
    }

    public function test_deliberation_message_score_calculates_upvotes_minus_downvotes(): void
    {
        $message = new DeliberationMessage([
            'upvotes' => 10,
            'downvotes' => 3,
        ]);

        $this->assertEquals(7, $message->score);
    }
}
