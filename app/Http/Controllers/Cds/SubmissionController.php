<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Cds\Submission;
use App\Models\Cds\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = [
            'title',
            'category',
            'status',
            'priority',
            'created_at',
        ];

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        if (! in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $submissions = Submission::query()
            ->with('submitter')

            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = trim($request->search);

                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            )

            ->when(
                $request->filled('status'),
                fn($query) => $query->where(
                    'status',
                    $request->status
                )
            )

            ->when(
                $request->filled('category'),
                fn($query) => $query->where(
                    'category',
                    $request->category
                )
            )

            ->orderBy($sortBy, $sortDirection)

            ->paginate(
                $request->integer('per_page', 20)
            )

            ->withQueryString();

        return Inertia::render(
            'Cds/Submissions/Index',
            [
                'submissions' => $submissions,

                'filters' => [
                    'search' => $request->search,
                    'status' => $request->status,
                    'category' => $request->category,
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                    'per_page' => $request->integer('per_page', 20),
                ],
            ]
        );
    }

    public function create()
    {
        return Inertia::render('Cds/Submissions/Create', [
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

        DB::beginTransaction();
        try {
            // generate embedding
            $embedding = EmbeddingService::getEmbedding($text);
            if (!is_null($embedding)) {
                // basic validation of embedding contents
                foreach ($embedding as $val) {
                    if (!is_numeric($val)) {
                        Log::error('Embedding contains non-numeric values', ['sample' => array_slice($embedding, 0, 3)]);
                        DB::rollBack();
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['title' => 'Unable to check for duplicate proposals at this time. Please try again later.']);
                    }
                }

                // check for near-duplicates using the embedding
                $threshold = (float) env('PROPOSAL_DUPLICATE_SIMILARITY', 0.9);
                $candidates = Submission::whereNotNull('embedding')->get();
                foreach ($candidates as $candidate) {
                    $other = $candidate->embedding;
                    if (is_array($other) && count($other) === count($embedding)) {
                        try {
                            $sim = EmbeddingService::cosineSimilarity($embedding, $other);
                        } catch (\Exception $e) {
                            Log::error('Error computing similarity', ['error' => $e->getMessage()]);
                            DB::rollBack();
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['title' => 'Unable to check for duplicate proposals at this time. Please try again later.']);
                        }

                        if ($sim >= $threshold) {
                            DB::rollBack();
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['title' => "A similar submission already exists (similarity: " . round($sim, 3) . "): {$candidate->title}"]);
                        }
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
                'embedding' => $embedding,
            ]);

            $submission = Submission::create($proposalData);

            DB::commit();

            return redirect()->route('cds.submissions.show', $submission)
                ->with('success', 'Submission created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Submission create error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'Unable to create submission at this time. Please try again later.']);
        }
    }

    public function show(Submission $submission)
    {
        $submission->load(['submitter', 'validationEvents.validator', 'decisionIssue']);

        return Inertia::render('Cds/Submissions/Show', [
            'submission' => $submission,
        ]);
    }

    public function edit(Submission $submission)
    {
        return Inertia::render('Cds/Submissions/Edit', [
            'submission' => $submission,
            'categories' => ['policy', 'resource', 'design', 'coordination', 'review'],
            'priorities' => ['low', 'normal', 'high', 'urgent'],
            'scopes' => ['local', 'regional', 'bioregional', 'global'],
        ]);
    }

    public function update(Request $request, Submission $submission)
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

        $submission->update($validated);

        return redirect()->route('cds.submissions.show', $submission)
            ->with('success', 'Submission updated successfully.');
    }

    public function submit(Submission $submission)
    {
        $submission->update(['status' => 'submitted']);

        return redirect()->route('cds.submissions.show', $submission)
            ->with('success', 'Submission submitted for validation.');
    }
}
