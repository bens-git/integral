<?php

use App\Http\Controllers\Csd\ProposalController;
use App\Http\Controllers\Csd\IssueController;
use App\Http\Controllers\Csd\DeliberationController;
use App\Http\Controllers\Csd\ConsensusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| CSD (Collaborative Decision System) Routes
|--------------------------------------------------------------------------
|
| These routes handle the CSD module functionality including proposals,
| issue framing, deliberation, consensus building, and decision tracking.
|
*/

Route::middleware(['auth', 'verified'])->prefix('csd')->name('csd.')->group(function () {
    
    // Dashboard
    Route::get('/', function () {
        return Inertia::render('Csd/Dashboard');
    })->name('dashboard');

    // Proposals
    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::get('/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
    Route::patch('/proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::post('/proposals/{proposal}/submit', [ProposalController::class, 'submit'])->name('proposals.submit');

    // Issues (Decision Issues)
    Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');
    Route::get('/issues/create', [IssueController::class, 'create'])->name('issues.create');
    Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');
    Route::get('/issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
    Route::post('/issues/{issue}/frame', [IssueController::class, 'frame'])->name('issues.frame');
    Route::post('/issues/{issue}/deliberation', [IssueController::class, 'startDeliberation'])->name('issues.start-deliberation');
    Route::post('/issues/{issue}/status', [IssueController::class, 'updateStatus'])->name('issues.update-status');

    // Deliberation
    Route::get('/issues/{issue}/deliberation', [DeliberationController::class, 'index'])->name('deliberation.index');
    Route::get('/deliberation/{thread}', [DeliberationController::class, 'show'])->name('deliberation.show');
    Route::post('/issues/{issue}/deliberation', [DeliberationController::class, 'store'])->name('deliberation.store');
    Route::post('/deliberation/{thread}/messages', [DeliberationController::class, 'sendMessage'])->name('deliberation.send-message');
    Route::patch('/messages/{message}', [DeliberationController::class, 'updateMessage'])->name('messages.update');
    Route::post('/messages/{message}/resolve', [DeliberationController::class, 'resolveMessage'])->name('messages.resolve');
    Route::post('/threads/{thread}/close', [DeliberationController::class, 'closeThread'])->name('threads.close');
    Route::post('/threads/{thread}/pin', [DeliberationController::class, 'pinThread'])->name('threads.pin');
    Route::post('/threads/{thread}/lock', [DeliberationController::class, 'lockThread'])->name('threads.lock');

    // Consensus
    Route::get('/issues/{issue}/consensus', [ConsensusController::class, 'show'])->name('consensus.show');
    Route::get('/issues/{issue}/consensus/create', [ConsensusController::class, 'create'])->name('consensus.create');
    Route::post('/issues/{issue}/consensus', [ConsensusController::class, 'store'])->name('consensus.store');
    Route::post('/consensus/{consensus}/vote', [ConsensusController::class, 'vote'])->name('consensus.vote');
    Route::post('/consensus/{consensus}/object', [ConsensusController::class, 'object'])->name('consensus.object');
    Route::post('/objections/{objection}/resolve', [ConsensusController::class, 'resolveObjection'])->name('objections.resolve');
    Route::post('/consensus/{consensus}/conclude', [ConsensusController::class, 'conclude'])->name('consensus.conclude');

    // Decisions (Ledger & Dispatch)
    Route::get('/decisions', function () {
        return Inertia::render('Csd/Decisions/Index');
    })->name('decisions.index');
    
    Route::get('/decisions/{ledger}', function () {
        return Inertia::render('Csd/Decisions/Show');
    })->name('decisions.show');
});