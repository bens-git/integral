<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Cds\SubmissionCluster;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClusterController extends Controller
{
    public function index(Request $request)
    {
        $clusters = SubmissionCluster::query()
            ->withCount('submissions')
            ->orderByDesc('submissions_count')
            ->paginate($request->integer('per_page', 25))
            ->withQueryString();

        return Inertia::render('Cds/Clusters/Index', [
            'clusters' => $clusters,
            'filters' => [
                'per_page' => $request->integer('per_page', 25),
            ],
        ]);
    }

    public function show(SubmissionCluster $cluster)
    {
        $cluster->load(['submissions.submitter']);

        return Inertia::render('Cds/Clusters/Show', [
            'cluster' => $cluster,
        ]);
    }
}
