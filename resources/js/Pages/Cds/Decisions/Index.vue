<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    decisions: Object,
});
</script>

<template>
    <Head title="Decision Ledger" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Decision Ledger
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Decision History</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            No decisions recorded yet. Decisions will appear here once consensus is reached on issues.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8">
                            <h4 class="text-md font-semibold text-gray-900 mb-4">About the Decision Ledger</h4>
                            <div class="prose prose-sm text-gray-500">
                                <p>
                                    The Decision Ledger provides an immutable record of all decisions made through the CDS system.
                                    Each entry includes a cryptographic hash for verification and links to related consensus processes,
                                    objections, and dispatch actions.
                                </p>
                                <ul class="mt-4 space-y-2">
                                    <li><strong>Created:</strong> Issue or proposal created</li>
                                    <li><strong>Framed:</strong> Problem statement defined</li>
                                    <li><strong>Deliberation Started:</strong> Community discussion begins</li>
                                    <li><strong>Consensus Reached:</strong> Decision approved</li>
                                    <li><strong>Dispatched:</strong> Decision sent to execution systems</li>
                                    <li><strong>Implemented:</strong> Decision put into practice</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>