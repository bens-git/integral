<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Cds\Proposal;
use App\Models\Cds\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Log;

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

        // create a combined text representation for embedding
        $text = trim($validated['title'] . "\n\n" . $validated['description']);

        try {
            $embedding = EmbeddingService::getEmbedding($text);
            if (empty($embedding) || !is_array($embedding)) {
                Log::error('Embedding service returned no embedding or invalid shape');
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['title' => 'Unable to check for duplicate proposals at this time. Please try again later.']);
            }
        } catch (\Exception $e) {
            Log::error('Embedding error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'Unable to check for duplicate proposals at this time. Please try again later.']);
        }

        // check for near-duplicates using the embedding
        $threshold = (float) env('PROPOSAL_DUPLICATE_SIMILARITY', 0.9);
        $candidates = Proposal::whereNotNull('embedding')->get();
        foreach ($candidates as $candidate) {
            $other = $candidate->embedding;
            if (is_array($other) && count($other) === count($embedding)) {
                $sim = EmbeddingService::cosineSimilarity($embedding, $other);
                if ($sim >= $threshold) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['title' => "A similar proposal already exists (similarity: " . round($sim, 3) . "): {$candidate->title}"]);
                }
            }
        }

        // Get or create participant for the authenticated user
        $participant = Participant::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'email' => Auth::user()->email]
        );

        $proposalData = array_merge($validated, [
            'submitter_id' => $participant->id,
            'status' => 'draft',
        ]);

        $proposalData['embedding'] = $embedding;

        $proposal = Proposal::create($proposalData);

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

        // recompute embedding when content changes
        $text = trim($validated['title'] . "\n\n" . $validated['description']);
        try {
            $embedding = EmbeddingService::getEmbedding($text);
        } catch (\Exception $e) {
            Log::error('Embedding error (update): ' . $e->getMessage());
            $embedding = null;
        }

        if ($embedding) {
            $validated['embedding'] = $embedding;
        }

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