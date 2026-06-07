<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cds\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Basic counts
        $usersCount = User::count();
        $proposalsCount = Submission::count();

        // Proposals by status
        $proposalsByStatus = Submission::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($r) => [ $r->status => $r->cnt ]);

        // Recent proposals
        $recentProposals = Submission::with('submitter')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get(['id','title','status','created_at','submitter_id']);

        return Inertia::render('Dashboard', [
            'user' => $user,
            'stats' => [
                'users' => $usersCount,
                'submissions' => $proposalsCount,
                'submissions_by_status' => $proposalsByStatus,
            ],
            'recentProposals' => $recentProposals,
        ]);
    }
}
