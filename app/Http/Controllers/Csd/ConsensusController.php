<?php

namespace App\Http\Controllers\Csd;

use App\Http\Controllers\Controller;
use App\Models\Csd\DecisionIssue;
use App\Models\Csd\ConsensusModel;
use App\Models\Csd\Objection;
use App\Models\Csd\Participant;
use App\Services\Csd\ConsensusEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConsensusController extends Controller
{
    public function __construct(
        private ConsensusEngine $consensusEngine
    ) {}

    public function show(DecisionIssue $issue)
    {
        $consensusModels = ConsensusModel::with(['objections.participant', 'facilitator'])
            ->where('issue_id', $issue->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Csd/Consensus/Show', [
            'issue' => $issue,
            'consensusModels' => $consensusModels,
        ]);
    }

    public function create(DecisionIssue $issue)
    {
        return Inertia::render('Csd/Consensus/Create', [
            'issue' => $issue,
            'methods' => ['weighted_consensus', 'condorcet', 'ranked_choice', 'consent'],
        ]);
    }

    public function store(DecisionIssue $issue, Request $request)
    {
        $validated = $request->validate([
            'method' => 'required|in:weighted_consensus,condorcet,ranked_choice,consent',
            'threshold' => 'required|integer|min:50|max:100',
            'summary' => 'nullable|string',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $consensus = ConsensusModel::create([
            'issue_id' => $issue->id,
            'method' => $validated['method'],
            'threshold' => $validated['threshold'],
            'summary' => $validated['summary'] ?? null,
            'facilitator_id' => $participant->id,
            'outcome' => 'pending',
            'voting_started_at' => now(),
        ]);

        $issue->update(['status' => 'consensus']);

        return redirect()->route('csd.consensus.show', ['issue' => $issue, 'consensus' => $consensus])
            ->with('success', 'Consensus process started.');
    }

    public function vote(ConsensusModel $consensus, Request $request)
    {
        $validated = $request->validate([
            'vote_type' => 'required|in:strong_support,support,neutral,concern,block',
            'rationale' => 'nullable|string',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        // Update vote counts based on vote type
        $fieldMap = [
            'strong_support' => 'votes_strong_support',
            'support' => 'votes_support',
            'neutral' => 'votes_neutral',
            'concern' => 'votes_concern',
            'block' => 'votes_block',
        ];

        $consensus->increment($fieldMap[$validated['vote_type']]);
        $consensus->increment('total_votes');

        // Recalculate consensus score
        $score = $this->consensusEngine->calculateScore($consensus);
        $consensus->update(['consensus_score' => $score]);

        // If blocking vote, create an objection
        if ($validated['vote_type'] === 'block' && $validated['rationale']) {
            Objection::create([
                'consensus_id' => $consensus->id,
                'participant_id' => $participant->id,
                'objection_strength' => 5.00,
                'reason' => $validated['rationale'],
                'is_blocking' => true,
                'status' => 'open',
            ]);

            $consensus->increment('blocking_objections');
        }

        return back()->with('success', 'Vote recorded.');
    }

    public function object(ConsensusModel $consensus, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'objection_strength' => 'required|numeric|min:0|max:5',
            'objection_type' => 'required|in:principled,practical,procedural,technical',
            'proposed_resolution' => 'nullable|string',
            'is_blocking' => 'boolean',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $objection = Objection::create([
            'consensus_id' => $consensus->id,
            'participant_id' => $participant->id,
            'objection_strength' => $validated['objection_strength'],
            'objection_type' => $validated['objection_type'],
            'reason' => $validated['reason'],
            'proposed_resolution' => $validated['proposed_resolution'] ?? null,
            'is_blocking' => $validated['is_blocking'] ?? false,
            'status' => 'open',
        ]);

        if ($objection->is_blocking) {
            $consensus->increment('blocking_objections');
        }

        return back()->with('success', 'Objection recorded.');
    }

    public function resolveObjection(Objection $objection, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:addressed,resolved,withdrawn,upheld',
            'resolution_notes' => 'nullable|string',
        ]);

        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $objection->update([
            'status' => $validated['status'],
            'addressed_by_id' => $participant->id,
            'resolution_notes' => $validated['resolution_notes'] ?? null,
            'addressed_at' => now(),
            'resolved_at' => now(),
        ]);

        // If resolved, decrement blocking objections
        if ($objection->is_blocking && in_array($validated['status'], ['resolved', 'withdrawn', 'upheld'])) {
            $objection->consensus->decrement('blocking_objections');
        }

        return back()->with('success', 'Objection resolved.');
    }

    public function conclude(ConsensusModel $consensus)
    {
        $outcome = $this->consensusEngine->determineOutcome($consensus);

        $consensus->update([
            'outcome' => $outcome,
            'voting_ended_at' => now(),
            'outcome_declared_at' => now(),
        ]);

        // Update issue status based on outcome
        if (in_array($outcome, ['consensus_reached', 'consent'])) {
            $consensus->issue->update([
                'status' => 'decided',
                'consensus_reached_at' => now(),
            ]);
        } elseif ($outcome === 'blocked') {
            $consensus->issue->update(['status' => 'deliberation']);
        }

        return back()->with('success', "Consensus concluded: {$outcome}");
    }
}