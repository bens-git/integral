<?php

namespace App\Services\Cds;

use App\Models\Csd\ConsensusModel;

class ConsensusEngine
{
    public function calculateScore(ConsensusModel $consensus): float
    {
        if ($consensus->total_votes === 0) {
            return 0.0;
        }

        $weights = [
            'strong_support' => 100,
            'support' => 80,
            'neutral' => 50,
            'concern' => 20,
            'block' => 0,
        ];

        $totalScore = (
            $consensus->votes_strong_support * $weights['strong_support'] +
            $consensus->votes_support * $weights['support'] +
            $consensus->votes_neutral * $weights['neutral'] +
            $consensus->votes_concern * $weights['concern'] +
            $consensus->votes_block * $weights['block']
        );

        return round($totalScore / $consensus->total_votes, 2);
    }

    public function determineOutcome(ConsensusModel $consensus): string
    {
        // Check for blocking objections
        if ($consensus->blocking_objections > 0) {
            return 'blocked';
        }

        // Check if consensus threshold is met
        if ($consensus->consensus_score !== null && $consensus->consensus_score >= $consensus->threshold) {
            return 'consensus_reached';
        }

        // For consent-based methods, check if there are any concerns
        if ($consensus->method === 'consent') {
            if ($consensus->votes_block === 0 && $consensus->votes_concern === 0) {
                return 'consent';
            }
            return 'blocked';
        }

        // Check if enough participants have voted
        if ($consensus->total_participants > 0 && $consensus->total_votes >= $consensus->total_participants * 0.8) {
            if ($consensus->consensus_score >= $consensus->threshold) {
                return 'consensus_reached';
            }
        }

        return 'pending';
    }

    public function hasConsensus(ConsensusModel $consensus): bool
    {
        return $consensus->consensus_score !== null 
            && $consensus->consensus_score >= $consensus->threshold
            && $consensus->blocking_objections === 0;
    }

    public function getBreakdown(ConsensusModel $consensus): array
    {
        if ($consensus->total_votes === 0) {
            return [
                'strong_support' => 0,
                'support' => 0,
                'neutral' => 0,
                'concern' => 0,
                'block' => 0,
            ];
        }

        return [
            'strong_support' => round(($consensus->votes_strong_support / $consensus->total_votes) * 100, 2),
            'support' => round(($consensus->votes_support / $consensus->total_votes) * 100, 2),
            'neutral' => round(($consensus->votes_neutral / $consensus->total_votes) * 100, 2),
            'concern' => round(($consensus->votes_concern / $consensus->total_votes) * 100, 2),
            'block' => round(($consensus->votes_block / $consensus->total_votes) * 100, 2),
        ];
    }

    public function getStatusSummary(ConsensusModel $consensus): array
    {
        return [
            'score' => $consensus->consensus_score,
            'threshold' => $consensus->threshold,
            'total_votes' => $consensus->total_votes,
            'total_participants' => $consensus->total_participants,
            'blocking_objections' => $consensus->blocking_objections,
            'outcome' => $consensus->outcome,
            'has_consensus' => $this->hasConsensus($consensus),
            'breakdown' => $this->getBreakdown($consensus),
            'participation_rate' => $consensus->total_participants > 0 
                ? round(($consensus->total_votes / $consensus->total_participants) * 100, 2) 
                : 0,
        ];
    }
}