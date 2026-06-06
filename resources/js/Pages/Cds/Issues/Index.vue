<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    issues: Object,
    filters: Object,
});

const statusColors = {
    draft: 'bg-gray-100 text-gray-800',
    framing: 'bg-yellow-100 text-yellow-800',
    deliberation: 'bg-blue-100 text-blue-800',
    consensus: 'bg-purple-100 text-purple-800',
    decided: 'bg-green-100 text-green-800',
    implemented: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800',
};

const decisionTypeColors = {
    policy: 'bg-blue-100 text-blue-800',
    resource_allocation: 'bg-green-100 text-green-800',
    design_approval: 'bg-purple-100 text-purple-800',
    coordination: 'bg-orange-100 text-orange-800',
    review: 'bg-gray-100 text-gray-800',
};

function filterByStatus(status) {
    router.get(route('cds.issues.index'), { status }, { preserveState: true });
}

function filterByType(type) {
    router.get(route('cds.issues.index'), { decision_type: type }, { preserveState: true });
}
</script>

<template>
    <Head title="Decision Issues" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Decision Issues
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-sm text-gray-500 mr-2">Filter by status:</span>
                            <button v-for="(color, status) in statusColors" :key="status" @click="filterByStatus(status)"
                                :class="[
                                    color,
                                    'px-3 py-1 rounded-full text-xs font-medium capitalize',
                                    filters?.status === status ? 'ring-2 ring-offset-2 ring-indigo-500' : ''
                                ]">
                                {{ status.replace('_', ' ') }}
                            </button>
                            <button v-if="filters?.status" @click="filterByStatus(null)"
                                class="px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-gray-300">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Issues List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">All Decision Issues</h3>

                        <div class="space-y-4">
                            <div v-for="issue in issues.data" :key="issue.id"
                                class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <Link :href="route('cds.issues.show', issue.id)"
                                                class="text-lg font-semibold text-gray-900 hover:text-blue-600">
                                                {{ issue.framed_problem }}
                                            </Link>
                                            <span :class="statusColors[issue.status] || 'bg-gray-100 text-gray-800'"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">
                                                {{ issue.status.replace('_', ' ') }}
                                            </span>
                                            <span :class="decisionTypeColors[issue.decision_type] || 'bg-gray-100 text-gray-800'"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ issue.decision_type.replace('_', ' ') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                            {{ issue.scope }}
                                        </p>
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span>Priority: {{ issue.priority }}/10</span>
                                            <span>Proposal: {{ issue.proposal?.title || 'N/A' }}</span>
                                            <span>Facilitator: {{ issue.facilitator?.name || 'Unassigned' }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <Link :href="route('cds.issues.show', issue.id)"
                                            class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                            View
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div v-if="issues.data.length === 0" class="text-center py-12 text-gray-500">
                                No decision issues found.
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="issues.data.length > 0" class="mt-6">
                            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                                <div class="flex w-0 flex-1">
                                    <Link v-if="issues.prev_page_url" :href="issues.prev_page_url"
                                        class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        Previous
                                    </Link>
                                </div>
                                <div class="hidden md:flex">
                                    <span class="text-sm text-gray-500">
                                        Page {{ issues.current_page }} of {{ issues.last_page }}
                                    </span>
                                </div>
                                <div class="flex w-0 flex-1 justify-end">
                                    <Link v-if="issues.next_page_url" :href="issues.next_page_url"
                                        class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        Next
                                    </Link>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>