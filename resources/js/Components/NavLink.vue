<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: String,
        default: null,
    },
})
</script>

<template>
    <Link
        :href="href"
        class="text-decoration-none"
    >
        <v-btn
            :prepend-icon="icon"
            :variant="active ? 'flat' : 'text'"
            :color="active ? 'secondary' : 'white'"
            rounded="lg"
        >
            <slot />
        </v-btn>
    </Link>
</template>