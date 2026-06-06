<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    proposals: Object,
    filters: Object,
});

const statusColors = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    validated: 'bg-green-100 text-green-800',
    framed: 'bg-purple-100 text-purple-800',
    active: 'bg-yellow-100 text-yellow-800',
    accepted: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800',
    implemented: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800',
};

function filterByStatus(status) {
    router.get(route('cds.proposals.index'), { status }, { preserveState: true });
}
</script>

<template>
    <Head title="Proposals" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Proposals
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-sm text-gray-500 mr-2">Filter by status:</span>
                            <button
                                v-for="(color, status) in statusColors"
                                :key="status"
                                @click="filterByStatus(status)"
                                :class="[
                                    color,
                                    'px-3 py-1 rounded-full text-xs font-medium capitalize',
                                    filters?.status === status ? 'ring-2 ring-offset-2 ring-indigo-500' : ''
                                ]"
                            >
                                {{ status }}
                            </button>
                            <button
                                v-if="filters?.status"
                                @click="filterByStatus(null)"
                                class="px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700 hover:bg-gray-300"
                            >
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Proposals List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">All Proposals</h3>
                            <Link :href="route('cds.proposals.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                New Proposal
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="proposal in proposals.data" :key="proposal.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <Link :href="route('cds.proposals.show', proposal.id)" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                {{ proposal.title }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500 capitalize">{{ proposal.category }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="statusColors[proposal.status] || 'bg-gray-100 text-gray-800'"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize">
                                                {{ proposal.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500 capitalize">{{ proposal.priority }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(proposal.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link :href="route('cds.proposals.show', proposal.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">
                                                View
                                            </Link>
                                            <Link v-if="proposal.status === 'draft'" :href="route('cds.proposals.submit', proposal.id)"
                                                class="text-green-600 hover:text-green-900">
                                                Submit
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="proposals.data.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            No proposals found. <Link :href="route('cds.proposals.create')" class="text-blue-600 hover:text-blue-900">Create one?</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="proposals.data.length > 0" class="mt-6">
                            <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
                                <div class="flex w-0 flex-1">
                                    <Link v-if="proposals.prev_page_url" :href="proposals.prev_page_url"
                                        class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        <svg class="mr-3 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Previous
                                    </Link>
                                </div>
                                <div class="hidden md:flex">
                                    <span class="text-sm text-gray-500">
                                        Page {{ proposals.current_page }} of {{ proposals.last_page }}
                                    </span>
                                </div>
                                <div class="flex w-0 flex-1 justify-end">
                                    <Link v-if="proposals.next_page_url" :href="proposals.next_page_url"
                                        class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        Next
                                        <svg class="ml-3 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
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