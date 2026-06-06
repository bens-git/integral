<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Csd\DecisionIssue;
use App\Models\Csd\Proposal;
use App\Models\Csd\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function index(Request $request)
    {
        $issues = DecisionIssue::with(['proposal', 'facilitator'])
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->has('decision_type'), function ($query) use ($request) {
                $query->where('decision_type', $request->decision_type);
            })
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Cds/Issues/Index', [
            'issues' => $issues,
            'filters' => $request->only(['status', 'decision_type']),
        ]);
    }

    public function show(DecisionIssue $issue)
    {
        $issue->load([
            'proposal.submitter',
            'facilitator',
            'scenarios',
            'deliberationThreads.messages.participant',
            'consensusModels',
            'feedbackEvents',
            'knowledgeMappings.knowledgeNode',
        ]);

        return Inertia::render('Cds/Issues/Show', [
            'issue' => $issue,
        ]);
    }

    public function create(Proposal $proposal = null)
    {
        return Inertia::render('Cds/Issues/Create', [
            'proposal' => $proposal,
            'decisionTypes' => ['policy', 'resource_allocation', 'design_approval', 'coordination', 'review'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'framed_problem' => 'required|string',
            'scope' => 'nullable|string',
            'success_criteria' => 'nullable|string',
            'constraints' => 'nullable|string',
            'priority' => 'required|integer|min:1|max:10',
            'decision_type' => 'required|in:policy,resource_allocation,design_approval,coordination,review',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $issue = DecisionIssue::create([
            ...$validated,
            'facilitator_id' => $participant->id,
            'status' => 'draft',
        ]);

        // Update proposal status
        $issue->proposal->update(['status' => 'framed']);

        return redirect()->route('cds.issues.show', $issue)
            ->with('success', 'Decision issue framed successfully.');
    }

    public function updateStatus(DecisionIssue $issue, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,framing,deliberation,consensus,decided,implemented,archived',
        ]);

        $issue->update($validated);

        return back()->with('success', 'Issue status updated.');
    }

    public function frame(DecisionIssue $issue)
    {
        $issue->update([
            'status' => 'framing',
            'framing_completed_at' => now(),
        ]);

        return redirect()->route('cds.issues.show', $issue)
            ->with('success', 'Issue framing completed.');
    }

    public function startDeliberation(DecisionIssue $issue)
    {
        $issue->update([
            'status' => 'deliberation',
            'deliberation_started_at' => now(),
        ]);

        return redirect()->route('cds.issues.show', $issue)
            ->with('success', 'Deliberation started.');
    }
}