<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\DeliberationThread;
use App\Models\Cds\DeliberationMessage;
use App\Models\Cds\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DeliberationController extends Controller
{
    public function index(DecisionIssue $issue)
    {
        $threads = DeliberationThread::with(['createdBy', 'messages.participant'])
            ->where('issue_id', $issue->id)
            ->whereNull('parent_id')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_activity_at', 'desc')
            ->paginate(20);

        return Inertia::render('Cds/Deliberation/Index', [
            'issue' => $issue,
            'threads' => $threads,
        ]);
    }

    public function show(DeliberationThread $thread)
    {
        $thread->load(['messages.participant', 'messages.children.participant']);

        return Inertia::render('Cds/Deliberation/Show', [
            'thread' => $thread,
        ]);
    }

    public function store(DecisionIssue $issue, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'topic' => 'nullable|string',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $thread = DeliberationThread::create([
            'issue_id' => $issue->id,
            'created_by_id' => $participant->id,
            'title' => $validated['title'],
            'topic' => $validated['topic'] ?? null,
            'status' => 'open',
        ]);

        return redirect()->route('cds.deliberation.show', $thread)
            ->with('success', 'Deliberation thread created.');
    }

    public function sendMessage(DeliberationThread $thread, Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'stance' => 'required|in:support,concern,objection,question,neutral,suggestion',
            'parent_id' => 'nullable|exists:deliberation_messages,id',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $message = DeliberationMessage::create([
            'thread_id' => $thread->id,
            'participant_id' => $participant->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'message' => $validated['message'],
            'stance' => $validated['stance'],
        ]);

        $thread->incrementMessageCount();

        return back()->with('success', 'Message sent.');
    }

    public function updateMessage(DeliberationMessage $message, Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message->update([
            'message' => $validated['message'],
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        return back()->with('success', 'Message updated.');
    }

    public function resolveMessage(DeliberationMessage $message)
    {
        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $message->update([
            'is_resolved' => true,
            'resolved_by_id' => $participant->id,
        ]);

        return back()->with('success', 'Message resolved.');
    }

    public function closeThread(DeliberationThread $thread)
    {
        $thread->update(['status' => 'closed']);

        return back()->with('success', 'Thread closed.');
    }

    public function pinThread(DeliberationThread $thread)
    {
        $thread->update(['is_pinned' => !$thread->is_pinned]);

        return back()->with('success', 'Thread pinned status updated.');
    }

    public function lockThread(DeliberationThread $thread)
    {
        $thread->update(['is_locked' => !$thread->is_locked]);

        return back()->with('success', 'Thread lock status updated.');
    }
}