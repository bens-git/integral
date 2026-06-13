<?php

use App\Http\Controllers\Cds\SubmissionController;
use App\Http\Controllers\Cds\IssueController;
use App\Http\Controllers\Cds\DeliberationController;
use App\Http\Controllers\Cds\ConsensusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| CDS (Collaborative Decision System) Routes
|--------------------------------------------------------------------------
|
| These routes handle the CDS module functionality including proposals,
| issue framing, deliberation, consensus building, and decision tracking.
|
*/

Route::middleware(['auth', 'verified'])->prefix('cds')->name('cds.')->group(function () {
    
    // Dashboard
    Route::get('/', [App\Http\Controllers\Cds\DashboardController::class, 'index'])->name('dashboard');

    // Submissions
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::patch('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::post('/submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');

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
        return Inertia::render('Cds/Decisions/Index');
    })->name('decisions.index');
    
    Route::get('/decisions/{ledger}', function () {
        return Inertia::render('Cds/Decisions/Show');
    })->name('decisions.show');
});