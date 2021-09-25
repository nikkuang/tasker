<template>
    <Head title="Task" />
    <BreezeAuthenticatedLayout>
        <template #header>Reports</template>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div v-if="reportData.length > 0" class="flex justify-end mb-1">
                    <BreezeDropdown align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    Download
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <a class="dropdown-link" :href="route('tasks.report.download')">
                                Excel
                            </a>
                            <a class="dropdown-link" :href="route('tasks.report.download', { filetype: 'csv' })">
                                CSV
                            </a>
                            <a class="dropdown-link" :href="route('tasks.report.download', { filetype: 'json' })">
                                JSON
                            </a>
                        </template>
                    </BreezeDropdown>
                </div>
                <div class="p-6 bg-white border-b border-gray-200 divide divide-y divide-gray-100 ">
                    <template v-if="reportData.length > 0">
                        <vue-highcharts
                            type="chart"
                            :options="chartOptions"
                            :redrawOnUpdate="true"
                            :oneToOneUpdate="false"
                            :animateOnUpdate="true" />
                    </template>
                    <template v-else>
                        No Data Available.
                    </template>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import BreezeDropdown from '@/Components/Dropdown.vue'
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import { Head } from '@inertiajs/inertia-vue3';
import VueHighcharts from 'vue3-highcharts';

export default {
    props: {
        reports: [Array, Object]
    },

    components: {
        BreezeAuthenticatedLayout,
        BreezeDropdown,
        BreezeDropdownLink,
        Head,
        VueHighcharts
    },

    computed: {
        chartOptions() {

            return {
                chart: {
                    type: 'pie',
                },
                title: {
                    text: 'Your productivity',
                },
                series: [{
                    name: 'Tasks',
                    data: this.reportData
                }],
            }
        },
        reportData() {
            return [
                {
                    name: 'Completed',
                    y: this.reports['completed_count'],
                    selected: true,
                    color: 'rgba(16, 185, 129, 1)'
                },
                {
                    name: 'Pending',
                    y: this.reports['pending_count'],
                    color: ' rgba(252, 211, 77, 1)'

                },
                {
                    name: 'Cancelled',
                    y: this.reports['cancelled_count'],
                    color: 'rgba(220, 38, 38, 1)'
                },
                {
                    name: 'Others',
                    y: this.reports['others_count'],
                    color: 'rgba(107, 114, 128, 1)'
                }
            ].filter(i => i.y > 0)
        }
    }
}
</script>
<style lang="postcss" scoped>
.dropdown-link {
    @apply block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out;
}
</style>
