<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const stats = [
    { name: 'Active Proposals', value: 12, href: '/cds/proposals?status=active', color: 'bg-blue-500' },
    { name: 'Issues in Deliberation', value: 5, href: '/cds/issues?status=deliberation', color: 'bg-green-500' },
    { name: 'Consensus Reached', value: 8, href: '/cds/issues?status=decided', color: 'bg-purple-500' },
    { name: 'Pending Decisions', value: 3, href: '/cds/decisions', color: 'bg-orange-500' },
];

const recentActivity = [
    { id: 1, type: 'proposal', title: 'Community Garden Initiative', action: 'submitted', time: '2 hours ago' },
    { id: 2, type: 'issue', title: 'Solar Panel Installation', action: 'entered deliberation', time: '5 hours ago' },
    { id: 3, type: 'consensus', title: 'Bike Lane Extension', action: 'consensus reached', time: '1 day ago' },
    { id: 4, type: 'decision', title: 'Tool Library Expansion', action: 'dispatched to COS', time: '2 days ago' },
];
</script>

<template>
    <Head title="CDS Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                CDS Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div v-for="stat in stats" :key="stat.name"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <Link :href="stat.href" class="block p-6">
                            <div class="flex items-center">
                                <div :class="stat.color" class="w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                                    <span class="text-white font-bold text-lg">{{ stat.value }}</span>
                                </div>
                                <div>
                                    <p class="text-gray-500 text-sm">{{ stat.name }}</p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="flex flex-wrap gap-4">
                            <Link :href="route('cds.proposals.create')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                New Proposal
                            </Link>
                            <Link :href="route('cds.proposals.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                View Proposals
                            </Link>
                            <Link :href="route('cds.issues.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                View Issues
                            </Link>
                            <Link :href="route('cds.decisions.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Decision Ledger
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                <li v-for="(activity, activityIdx) in recentActivity" :key="activity.id">
                                    <div class="relative pb-8">
                                        <span v-if="activityIdx !== recentActivity.length - 1"
                                            class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                            aria-hidden="true" />
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-gray-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        <span class="font-medium text-gray-900">{{ activity.title }}</span>
                                                        was {{ activity.action }}
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time :datetime="activity.time">{{ activity.time }}</time>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>