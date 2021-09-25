<template>
    <Head title="Task" />
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200 ">
                    <h1 class="font-semibold text-lg">Tasks</h1>
                    <TaskManager
                        :tasks="tasks"
                        @status-updated="$inertia.reload(route('tasks.index'))">
                    </TaskManager>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import TaskManager from '@/Pages/Task/Components/TaskManager.vue';

export default {
    props: {
        tasks: [Array, Object],
        filter: [Array, Object],
    },

    components: {
        BreezeAuthenticatedLayout,
        Head,
        TaskManager
    }
}
</script>

