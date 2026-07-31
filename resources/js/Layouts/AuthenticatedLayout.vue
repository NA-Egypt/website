<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const showingNavigationDropdown = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link href="/" class="font-bold text-xl text-primary">
                                NA Egypt
                            </Link>
                        </div>
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <div v-if="page.props.auth.user" class="text-sm font-medium text-gray-700">
                            {{ page.props.auth.user.name }}
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash messages notification area -->
        <div v-if="page.props.flash?.success" class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ page.props.flash.success }}
            </div>
        </div>

        <main>
            <slot />
        </main>
    </div>
</template>
