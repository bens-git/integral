<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm, Head } from '@inertiajs/vue3';

defineProps({
    categories: Array,
    priorities: Array,
    scopes: Array,
});

const form = useForm({
    title: '',
    description: '',
    content: '',
    category: 'policy',
    priority: 'normal',
    scope: null,
});

function submit() {
    form.post(route('csd.proposals.store'));
}
</script>

<template>
    <Head title="New Proposal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create New Proposal
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Title -->
                            <div class="mb-6">
                                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                                <input id="title" v-model="form.title" type="text" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.title }" />
                                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="description" v-model="form.description" rows="3" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.description }"></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Content -->
                            <div class="mb-6">
                                <label for="content" class="block text-sm font-medium text-gray-700">Full Content (Optional)</label>
                                <textarea id="content" v-model="form.content" rows="6"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                <p class="mt-1 text-sm text-gray-500">Provide detailed information about your proposal</p>
                            </div>

                            <!-- Category -->
                            <div class="mb-6">
                                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                                <select id="category" v-model="form.category" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.category }">
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category.charAt(0).toUpperCase() + category.slice(1).replace('_', ' ') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div class="mb-6">
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                <select id="priority" v-model="form.priority" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    :class="{ 'border-red-300': form.errors.priority }">
                                    <option v-for="priority in priorities" :key="priority" :value="priority">
                                        {{ priority.charAt(0).toUpperCase() + priority.slice(1) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Scope -->
                            <div class="mb-6">
                                <label for="scope" class="block text-sm font-medium text-gray-700">Scope (Optional)</label>
                                <select id="scope" v-model="form.scope"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option :value="null">Select scope...</option>
                                    <option v-for="scope in scopes" :key="scope" :value="scope">
                                        {{ scope.charAt(0).toUpperCase() + scope.slice(1) }}
                                    </option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('csd.proposals.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancel
                                </Link>
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25">
                                    {{ form.processing ? 'Creating...' : 'Create Proposal' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>