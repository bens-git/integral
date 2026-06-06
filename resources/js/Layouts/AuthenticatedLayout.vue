<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'

const drawer = ref(false)

const navigation = [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: 'mdi-view-dashboard',
    },
    {
        name: 'CDS',
        route: 'cds.dashboard',
        icon: 'mdi-account-group',
    },
]
</script>

<template>
    <v-app>
        <!-- App Bar -->
        <v-app-bar
            elevation="2"
            color="primary"
            density="comfortable"
        >
            <v-app-bar-nav-icon
                class="d-md-none"
                @click="drawer = !drawer"
            />

            <!-- Logo -->
            <Link
                :href="route('dashboard')"
                class="d-flex align-center text-decoration-none"
            >
                <ApplicationLogo
                    class="mr-3"
                    style="height: 36px"
                />

                <span
                    class="text-h6 font-weight-bold text-white"
                >
                    Integral CDS
                </span>
            </Link>

            <v-spacer />

            <!-- Desktop Menu -->
            <div
                class="d-none d-md-flex align-center ga-2"
            >
                <NavLink
                    v-for="item in navigation"
                    :key="item.route"
                    :href="route(item.route)"
                    :active="route().current(item.route)"
                    :icon="item.icon"
                >
                    {{ item.name }}
                </NavLink>
            </div>

            <v-spacer />

            <!-- User Menu -->
            <Dropdown
                align="right"
                width="56"
            >
                <template #trigger>
                    <v-btn
                        color="white"
                        variant="text"
                        append-icon="mdi-chevron-down"
                    >
                        {{ $page.props.auth.user.name }}
                    </v-btn>
                </template>

                <template #content>
                    <DropdownLink
                        :href="route('profile.edit')"
                    >
                        Profile
                    </DropdownLink>

                    <DropdownLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Logout
                    </DropdownLink>
                </template>
            </Dropdown>
        </v-app-bar>

        <!-- Mobile Drawer -->
        <v-navigation-drawer
            v-model="drawer"
            temporary
        >
            <v-list nav>
                <v-list-item
                    class="mb-4"
                >
                    <template #prepend>
                        <ApplicationLogo
                            style="height: 32px"
                        />
                    </template>

                    <v-list-item-title>
                        Integral CDS
                    </v-list-item-title>
                </v-list-item>

                <Link
                    v-for="item in navigation"
                    :key="item.route"
                    :href="route(item.route)"
                    class="text-decoration-none"
                >
                    <v-list-item
                        :prepend-icon="item.icon"
                        :title="item.name"
                    />
                </Link>
            </v-list>
        </v-navigation-drawer>

        <!-- Main -->
        <v-main>
            <template v-if="$slots.header">
                <v-sheet
                    border
                    class="bg-white"
                >
                    <v-container
                        class="py-5"
                    >
                        <slot name="header" />
                    </v-container>
                </v-sheet>
            </template>

            <v-container
                fluid
                class="pa-6"
            >
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>