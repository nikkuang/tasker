<template>
    <div class="divide divide-y divide-gray-100 mt-3">
        <TaskList v-if="tasks.length" :data="tasks" @status-updated="$emit('status-updated', task)"></TaskList>
        <div class="p-3" v-if="canAddTask">
            <a
                type="button"
                v-if="!showEditor"
                @click="showEditor = true"
                class="inline-flex justify-center items-center cursor-pointer">
                <PlusIcon class="h-5 w-5 mr-2" /> Add task
            </a>

            <TaskEditor
                v-else
                @cancel="showEditor = !showEditor"
                :parentTask="parentTask">
            </TaskEditor>
        </div>
    </div>
</template>
<script>
import TaskList from '@/Pages/Task/Components/TaskList.vue';
import TaskEditor from '@/Pages/Task/Components/TaskEditor.vue';
import { PlusIcon } from '@heroicons/vue/solid'

export default {
    props: {
        tasks: Array,
        parentTask: {
            type: Object,
            required: false,
            default: null
        },
        canAddTask: {
            type: Boolean,
            default: true
        }
    },
    components: {
        TaskList,
        TaskEditor,
        PlusIcon
    },
    data () {
        return {
            showEditor: false
        }
    },
}
</script>
