<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Csd\Proposal;
use App\Models\Csd\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProposalController extends Controller
{
    public function index(Request $request)
    {
        $proposals = Proposal::with(['submitter'])
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->has('category'), function ($query) use ($request) {
                $query->where('category', $request->category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Cds/Proposals/Index', [
            'proposals' => $proposals,
            'filters' => $request->only(['status', 'category']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Cds/Proposals/Create', [
            'categories' => ['policy', 'resource', 'design', 'coordination', 'review'],
            'priorities' => ['low', 'normal', 'high', 'urgent'],
            'scopes' => ['local', 'regional', 'bioregional', 'global'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'category' => 'required|in:policy,resource,design,coordination,review',
            'priority' => 'required|in:low,normal,high,urgent',
            'scope' => 'nullable|in:local,regional,bioregional,global',
        ]);

        // Get or create participant for the authenticated user
        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $proposal = Proposal::create([
            ...$validated,
            'submitter_id' => $participant->id,
            'status' => 'draft',
        ]);

        return redirect()->route('cds.proposals.show', $proposal)
            ->with('success', 'Proposal created successfully.');
    }

    public function show(Proposal $proposal)
    {
        $proposal->load(['submitter', 'validationEvents.validator', 'decisionIssue']);

        return Inertia::render('Cds/Proposals/Show', [
            'proposal' => $proposal,
        ]);
    }

    public function edit(Proposal $proposal)
    {
        return Inertia::render('Cds/Proposals/Edit', [
            'proposal' => $proposal,
            'categories' => ['policy', 'resource', 'design', 'coordination', 'review'],
            'priorities' => ['low', 'normal', 'high', 'urgent'],
            'scopes' => ['local', 'regional', 'bioregional', 'global'],
        ]);
    }

    public function update(Request $request, Proposal $proposal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'category' => 'required|in:policy,resource,design,coordination,review',
            'priority' => 'required|in:low,normal,high,urgent',
            'scope' => 'nullable|in:local,regional,bioregional,global',
        ]);

        $proposal->update($validated);

        return redirect()->route('cds.proposals.show', $proposal)
            ->with('success', 'Proposal updated successfully.');
    }

    public function submit(Proposal $proposal)
    {
        $proposal->update(['status' => 'submitted']);

        return redirect()->route('cds.proposals.show', $proposal)
            ->with('success', 'Proposal submitted for validation.');
    }
}