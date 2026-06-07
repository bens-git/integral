<?php

namespace App\Http\Controllers\Cds;

use App\Http\Controllers\Controller;
use App\Models\Cds\Submission;
use App\Models\Cds\DecisionIssue;
use App\Models\Cds\DecisionDispatch;
use App\Models\Cds\DeliberationMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $stats = [
            'active_submissions' => Submission::count(),
            'issues_in_deliberation' => DecisionIssue::where('status', 'deliberation')->count(),
            'consensus_reached' => DecisionIssue::whereNotNull('consensus_reached_at')->count(),
            'pending_decisions' => DecisionDispatch::where('status', 'pending')->count(),
        ];

        // Proposals by status (for status breakdown)
        $proposalsByStatus = Submission::selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($r) { return [ $r->status => $r->cnt ]; });

        // attach to stats
        $stats['submissions_by_status'] = $proposalsByStatus;

        // Recent proposals
        $recentProposals = Submission::orderByDesc('created_at')
            ->limit(6)
            ->get(['id','title','created_at','submission_type'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'type' => $p->submission_type ?? 'submission',
                    'title' => $p->title,
                    'action' => 'submitted',
                    'created_at' => $p->created_at,
                ];
            });

        // Recent deliberation messages
        $recentMessages = DeliberationMessage::orderByDesc('created_at')
            ->limit(6)
            ->get(['id','message','created_at'])
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => 'message',
                    'title' => mb_strimwidth(strip_tags($m->message ?? ''), 0, 80, '...'),
                    'action' => 'commented',
                    'created_at' => $m->created_at,
                ];
            });

        // Merge and sort recent activity
        $recent = collect([$recentProposals, $recentMessages])->flatten(1)
            ->sortByDesc('created_at')
            ->take(8)
            ->map(function ($item) {
                return array_merge($item, ['time' => $item['created_at']->diffForHumans()]);
            })
            ->values();

        return Inertia::render('Cds/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recent,
        ]);
    }
}
