<template>
    <SlickItem class="group py-3 list-none flex items-center bg-white" tag="li">
        <SelectorIcon v-if="!editMode" v-handle class="task-item-handle h-5 w-5 cursor-move mr-1 opacity-0 group-hover:opacity-100"/>
        <div class="w-full flex justify-between items-center cursor-pointer" @click.self="itemClick">
            <div v-if="!editMode" @click="itemClick">
                <p class="w-full break-all cursor-pointer">{{ task.content }}</p>
                <span v-if="task.subtasks_count > 0" class="mt-1 text-xs flex align-middle text-gray-500">
                    <SubTaskIcon class="mr-1"></SubTaskIcon> {{task.subtasks_completed_count}}/{{ task.subtasks_count }}
                </span>
            </div>

            <TaskEditor
                v-else
                @cancel="hideEditor"
                @success="hideEditor"
                :task="task"
                class="w-full">
            </TaskEditor>

            <div v-if="!editMode" class="opacity-100 group-hover:opacity-100 flex align-middle">
                <TaskStatusSelector :task="task" :key="task.id" @updated="$emit('status-updated', $emit)"/>
                <BreezeDropdown align="right" width="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <DotsHorizontalIcon class="h-5 w-5" />
                            </button>
                        </span>
                    </template>

                    <template #content>
                        <BreezeDropdownLink href="#"  as="button" @click.stop.prevent="showEditor" preserve-state>
                            Edit
                        </BreezeDropdownLink>
                        <BreezeDropdownLink :href="route('tasks.trash', task.id)" method="delete" as="button">
                            Move to trash
                        </BreezeDropdownLink>
                    </template>
                </BreezeDropdown>
            </div>
        </div>
    </SlickItem>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import BreezeDropdown from '@/Components/Dropdown.vue'
import SubTaskIcon from '@/Pages/Task/Components/SubTaskIcon.vue';
import TaskEditor from '@/Pages/Task/Components/TaskEditor.vue';
import TaskStatusSelector from '@/Pages/Task/Components/TaskStatusSelector.vue';
import { SlickItem } from 'vue-slicksort';
import { HandleDirective } from 'vue-slicksort';
import { DotsHorizontalIcon, SelectorIcon } from '@heroicons/vue/solid'

export default {

    components: {
        Link,
        BreezeDropdownLink,
        BreezeDropdown,
        TaskEditor,
        SlickItem,
        TaskStatusSelector,
        DotsHorizontalIcon,
        SubTaskIcon,
        SelectorIcon
    },

    directives: { handle: HandleDirective },

    props: {
        task: Object
    },

    data() {
        return {
            editMode: false
        }
    },

    methods: {
        showEditor() {
            this.editMode = true;
            this.$emit('editor-shown');
        },
        hideEditor() {
            this.editMode = false;
            this.$emit('editor-hidden');
        },
        itemClick() {
            if (this.editMode) {
                return;
            }

            this.$inertia.visit(route('tasks.show', this.task.id));
        }
    }
}
</script>
