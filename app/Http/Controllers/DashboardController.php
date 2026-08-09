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

        // User position data
        $position = [
            'credits' => 630,
            'energy_kwh' => 4200,
            'consensus_pct' => 92,
            'members_count' => 47,
            'level' => 94,
        ];

        // Current needs - mock data for now
        $currentNeeds = [
            [
                'id' => 1,
                'icon' => '🌱',
                'title' => 'Water pump repair',
                'progress' => 12,
                'total' => 18,
                'credits' => 6,
                'status' => '2h remaining',
            ],
            [
                'id' => 2,
                'icon' => '☀',
                'title' => 'Solar materials',
                'progress' => 8,
                'total' => 14,
                'credits' => 24,
                'status' => 'awaiting fulfillment',
            ],
            [
                'id' => 3,
                'icon' => '🍎',
                'title' => 'Food redistribution',
                'progress' => 5,
                'total' => 10,
                'credits' => 3,
                'status' => 'local',
            ],
        ];

        // User tasks
        $tasks = [
            [
                'id' => 1,
                'title' => 'Review pump request',
                'completed' => false,
                'due' => '4h',
            ],
            [
                'id' => 2,
                'title' => 'Inventory materials',
                'completed' => false,
                'overdue' => true,
            ],
            [
                'id' => 3,
                'title' => 'Food pickup',
                'completed' => true,
            ],
        ];

        // Resources
        $resources = [
            [
                'name' => 'ENERGY',
                'value' => '4,200 kWh',
            ],
            [
                'name' => 'MATERIALS',
                'value' => '82% available',
            ],
            [
                'name' => 'FOOD',
                'value' => '94% available',
            ],
        ];

        // Community goals
        $goals = [
            [
                'title' => 'Community solar capacity',
                'progress' => 78,
            ],
            [
                'title' => 'Local food independence',
                'progress' => 54,
            ],
        ];

        return Inertia::render('Dashboard', [
            'user' => $user,
            'position' => $position,
            'currentNeeds' => $currentNeeds,
            'tasks' => $tasks,
            'resources' => $resources,
            'goals' => $goals,
        ]);
    }
}
