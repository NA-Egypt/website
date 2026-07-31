<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Select as VueSelect } from 'vue3-select-component';

const props = defineProps({
    title: String,
    cities: Array,
});

const selectedCity = ref('');

const cityOptions = (props.cities || [
    { label: 'القاهرة', value: 'cairo' },
    { label: 'الإسكندرية', value: 'alexandria' },
    { label: 'الجيزة', value: 'giza' }
]);
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-4">لوحة التحكم (Dashboard)</h1>
                    <p class="mb-6">مرحباً بك في نظام زمالة مدمني خمر مجهولين - مصر</p>

                    <div class="mb-6 max-w-md">
                        <label class="block mb-2 text-sm font-medium text-gray-900">اختيار المدينة (Vue3 Select Component)</label>
                        <VueSelect
                            v-model="selectedCity"
                            :options="cityOptions"
                            placeholder="اختر المدينة..."
                            is-searchable
                        />
                        <p class="mt-2 text-sm text-gray-500" v-if="selectedCity">المدينة المحددة: {{ selectedCity }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
