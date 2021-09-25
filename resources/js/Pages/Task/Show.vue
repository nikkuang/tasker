<template>
    <Head title="Edit Task" />
    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white border-b border-gray-200">

                    <Link class="back-link" v-if="task.parent" :href="route('tasks.show', task.parent.id)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-git-pull-request"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M13 6h3a2 2 0 0 1 2 2v7M6 9v12"/></svg>
                        <span>{{ task.parent.content }}</span>
                    </Link>
                    <Link class="back-link" v-else :href="route('tasks.index')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                        <span>Tasks</span>
                    </Link>

                    <div class="divide divide-y divide-gray-100">
                        <div v-if="!showEditor" class="flex">
                            <div class="pb-8 w-full break-all"  @click="showEditor = true">
                                <p class="font-extrabold">{{ task.content }}</p>
                            </div>
                            <div class="flex align-middle">
                                <TaskStatusSelector :task="task" @updated="statusUpdated" />
                                <BreezeDropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <BreezeDropdownLink href="#"  as="button" @click.stop.prevent="showEditor = true" preserve-state>
                                            Edit
                                        </BreezeDropdownLink>
                                        <BreezeDropdownLink :href="route('tasks.trash', task.id)" method="delete" as="button">
                                            Move to trash
                                        </BreezeDropdownLink>
                                    </template>
                                </BreezeDropdown>
                            </div>
                        </div>

                        <TaskEditor
                            v-else
                            @cancel="showEditor = false"
                            @success="showEditor = false"
                            :task="task"
                            :asSubtask="false"
                            class="w-full pb-4" />

                        <div class="pt-4 ">
                            <h2 class="font-extrabold">Sub-tasks
                                <span class="ml-2 font-normal text-xs" v-if="task.subtasks.length > 0">({{ task.subtasks.length }})</span>
                            </h2>
                            <TaskManager
                                :parentTask="task"
                                :tasks="task.subtasks"
                                @status-updated="statusUpdated"
                                :canAddTask="task.status !== 'cancelled'"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import TaskManager from '@/Pages/Task/Components/TaskManager.vue';
import TaskEditor from '@/Pages/Task/Components/TaskEditor.vue';
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import BreezeDropdown from '@/Components/Dropdown.vue'
import TaskStatusSelector from '@/Pages/Task/Components/TaskStatusSelector.vue';

export default {
    props: {
        task: Object
    },
    components: {
        Head,
        Link,
        BreezeAuthenticatedLayout,
        BreezeDropdownLink,
        BreezeDropdown,
        TaskManager,
        TaskEditor,
        TaskStatusSelector
    },

    data() {
        return {
            showEditor: false
        }
    },

    methods: {
        statusUpdated() {
            this.$inertia.reload(route('tasks.show', this.task.id));
        }
    }
}
</script>
<style lang="postcss" scoped>
.back-link {
    @apply text-xs font-light text-gray-600 mb-2 flex align-middle;
}

.back-link svg {
    @apply mr-2;
}
.back-link span {
    @apply overflow-hidden overflow-ellipsis truncate;
}
</style>
