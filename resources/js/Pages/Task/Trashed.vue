<template>
    <Head title="Task" />
    <BreezeAuthenticatedLayout>
        <template #header>Trash</template>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200 divide divide-y divide-gray-100 ">
                    <template v-if="tasks.length > 0">
                        <TaskTrashedItem v-for="task in tasks" :key="task.id" :task="task"/>
                    </template>
                    <template v-else>
                        Your trash is empty.
                    </template>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head } from '@inertiajs/inertia-vue3';
import TaskTrashedItem from '@/Pages/Task/Components/TaskTrashedItem.vue';

export default {
    props: {
        tasks: [Array, Object]
    },

    components: {
        BreezeAuthenticatedLayout,
        Head,
        TaskTrashedItem
    }
}
</script>
