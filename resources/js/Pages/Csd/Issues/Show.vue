<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    issue: Object,
});

const activeTab = ref('overview');

const tabs = [
    { id: 'overview', label: 'Overview' },
    { id: 'deliberation', label: 'Deliberation' },
    { id: 'consensus', label: 'Consensus' },
    { id: 'knowledge', label: 'Knowledge' },
];

const statusForm = useForm({
    status: props.issue.status,
});

function updateStatus() {
    statusForm.post(route('csd.issues.update-status', props.issue.id));
}

const statusColors = {
    draft: 'bg-gray-100 text-gray-800',
    framing: 'bg-yellow-100 text-yellow-800',
    deliberation: 'bg-blue-100 text-blue-800',
    consensus: 'bg-purple-100 text-purple-800',
    decided: 'bg-green-100 text-green-800',
    implemented: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <Head :title="issue.framed_problem" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Decision Issue
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h1 class="text-2xl font-bold text-gray-900">
                                        {{ issue.framed_problem }}
                                    </h1>
                                    <span :class="statusColors[issue.status] || 'bg-gray-100 text-gray-800'"
                                        class="px-3 py-1 inline-flex text-sm font-semibold rounded-full capitalize">
                                        {{ issue.status.replace('_', ' ') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-6 text-sm text-gray-500">
                                    <span>Priority: {{ issue.priority }}/10</span>
                                    <span>Type: {{ issue.decision_type.replace('_', ' ') }}</span>
                                    <span>Facilitator: {{ issue.facilitator?.name || 'Unassigned' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link v-if="issue.status === 'draft' || issue.status === 'framing'"
                                    :href="route('csd.issues.frame', issue.id)"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 transition">
                                    Frame Issue
                                </Link>
                                <Link v-if="issue.status === 'framing'"
                                    :href="route('csd.issues.start-deliberation', issue.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                    Start Deliberation
                                </Link>
                                <Link v-if="issue.status === 'deliberation'"
                                    :href="route('csd.consensus.create', { issue: issue.id })"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition">
                                    Start Consensus
                                </Link>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Scope</h3>
                            <p class="text-gray-600">{{ issue.scope || 'No scope defined' }}</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Success Criteria</h3>
                            <p class="text-gray-600">{{ issue.success_criteria || 'No success criteria defined' }}</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Constraints</h3>
                            <p class="text-gray-600">{{ issue.constraints || 'No constraints defined' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px">
                            <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                                :class="[
                                    activeTab === tab.id
                                        ? 'border-indigo-500 text-indigo-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm'
                                ]">
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- Overview Tab -->
                        <div v-if="activeTab === 'overview'" class="space-y-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Related Proposal</h3>
                                <div v-if="issue.proposal" class="bg-gray-50 rounded-lg p-4">
                                    <Link :href="route('csd.proposals.show', issue.proposal.id)"
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ issue.proposal.title }}
                                    </Link>
                                    <p class="text-sm text-gray-500 mt-1">{{ issue.proposal.description }}</p>
                                </div>
                                <p v-else class="text-gray-500">No proposal linked</p>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Scenarios</h3>
                                <div v-if="issue.scenarios && issue.scenarios.length > 0" class="space-y-3">
                                    <div v-for="scenario in issue.scenarios" :key="scenario.id"
                                        class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="font-medium text-gray-900">{{ scenario.title }}</h4>
                                        <p class="text-sm text-gray-500 mt-1">{{ scenario.description }}</p>
                                        <div class="flex gap-4 mt-2 text-xs text-gray-400">
                                            <span>Viability: {{ scenario.viability_score }}</span>
                                            <span>Risk: {{ scenario.risk_score }}</span>
                                            <span>Impact: {{ scenario.impact_score }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p v-else class="text-gray-500">No scenarios created yet</p>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Timeline</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Created:</span>
                                        <span>{{ new Date(issue.created_at).toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Framing Completed:</span>
                                        <span>{{ issue.framing_completed_at ? new Date(issue.framing_completed_at).toLocaleString() : 'Pending' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Deliberation Started:</span>
                                        <span>{{ issue.deliberation_started_at ? new Date(issue.deliberation_started_at).toLocaleString() : 'Not started' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Consensus Reached:</span>
                                        <span>{{ issue.consensus_reached_at ? new Date(issue.consensus_reached_at).toLocaleString() : 'Not reached' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deliberation Tab -->
                        <div v-if="activeTab === 'deliberation'" class="space-y-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Deliberation Threads</h3>
                                <Link :href="route('csd.deliberation.store', { issue: issue.id })"
                                    class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                    New Thread
                                </Link>
                            </div>

                            <div v-if="issue.deliberation_threads && issue.deliberation_threads.length > 0"
                                class="space-y-3">
                                <div v-for="thread in issue.deliberation_threads" :key="thread.id"
                                    class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span v-if="thread.is_pinned" class="text-yellow-500">📌</span>
                                                <span v-if="thread.is_locked" class="text-gray-400">🔒</span>
                                                <Link :href="route('csd.deliberation.show', thread.id)"
                                                    class="font-medium text-gray-900 hover:text-blue-600">
                                                    {{ thread.title }}
                                                </Link>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Started by {{ thread.created_by?.name || 'Unknown' }} •
                                                {{ thread.message_count }} messages •
                                                {{ thread.status }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">No deliberation threads yet</p>
                        </div>

                        <!-- Consensus Tab -->
                        <div v-if="activeTab === 'consensus'" class="space-y-6">
                            <div v-if="issue.consensus_models && issue.consensus_models.length > 0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Consensus Processes</h3>
                                <div class="space-y-4">
                                    <div v-for="consensus in issue.consensus_models" :key="consensus.id"
                                        class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <span class="font-medium">{{ consensus.method.replace('_', ' ') }}</span>
                                                <span :class="consensus.outcome === 'consensus_reached' ? 'bg-green-100 text-green-800' :
                                                    consensus.outcome === 'blocked' ? 'bg-red-100 text-red-800' :
                                                        'bg-yellow-100 text-yellow-800'"
                                                    class="ml-2 px-2 py-1 text-xs rounded-full capitalize">
                                                    {{ consensus.outcome.replace('_', ' ') }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-500">Score: {{ consensus.consensus_score || 'N/A' }}</span>
                                        </div>

                                        <!-- Vote breakdown -->
                                        <div class="grid grid-cols-5 gap-2 text-center text-sm">
                                            <div class="bg-green-50 rounded p-2">
                                                <div class="font-bold text-green-700">{{ consensus.votes_strong_support }}</div>
                                                <div class="text-xs text-green-600">Strong Support</div>
                                            </div>
                                            <div class="bg-green-50 rounded p-2">
                                                <div class="font-bold text-green-700">{{ consensus.votes_support }}</div>
                                                <div class="text-xs text-green-600">Support</div>
                                            </div>
                                            <div class="bg-gray-50 rounded p-2">
                                                <div class="font-bold text-gray-700">{{ consensus.votes_neutral }}</div>
                                                <div class="text-xs text-gray-600">Neutral</div>
                                            </div>
                                            <div class="bg-yellow-50 rounded p-2">
                                                <div class="font-bold text-yellow-700">{{ consensus.votes_concern }}</div>
                                                <div class="text-xs text-yellow-600">Concern</div>
                                            </div>
                                            <div class="bg-red-50 rounded p-2">
                                                <div class="font-bold text-red-700">{{ consensus.votes_block }}</div>
                                                <div class="text-xs text-red-600">Block</div>
                                            </div>
                                        </div>

                                        <!-- Objections -->
                                        <div v-if="consensus.objections && consensus.objections.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Objections ({{ consensus.blocking_objections }} blocking)</h4>
                                            <div class="space-y-2">
                                                <div v-for="objection in consensus.objections" :key="objection.id"
                                                    class="bg-red-50 rounded p-2 text-sm">
                                                    <div class="flex items-center justify-between">
                                                        <span class="font-medium">{{ objection.participant?.name || 'Anonymous' }}</span>
                                                        <span class="text-xs text-gray-500 capitalize">{{ objection.status }}</span>
                                                    </div>
                                                    <p class="text-gray-600 mt-1">{{ objection.reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">No consensus processes started yet</p>
                        </div>

                        <!-- Knowledge Tab -->
                        <div v-if="activeTab === 'knowledge'" class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Knowledge Base</h3>
                            <div v-if="issue.knowledge_mappings && issue.knowledge_mappings.length > 0" class="space-y-3">
                                <div v-for="mapping in issue.knowledge_mappings" :key="mapping.id"
                                    class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <span :class="mapping.relevance === 'supporting' ? 'bg-green-100 text-green-800' :
                                                mapping.relevance === 'contradicting' ? 'bg-red-100 text-red-800' :
                                                    'bg-gray-100 text-gray-800'"
                                                class="px-2 py-1 text-xs rounded-full capitalize">
                                                {{ mapping.relevance }}
                                            </span>
                                            <h4 class="font-medium text-gray-900 mt-2">{{ mapping.knowledge_node?.title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">{{ mapping.knowledge_node?.summary }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500">No knowledge nodes linked yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>