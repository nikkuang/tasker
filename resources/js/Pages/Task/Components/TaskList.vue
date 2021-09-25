<template>
    <SlickList class="divide divide-y divide-gray-100 bg-gray-100"
        :class="{ 'task-list-disabled' : itemOnEditMode }"
        useDragHandle
        lockAxis="y"
        v-model:list="data"
        tag="ul"
        :transitionDuration="0"
        :useWindowAsScrollContainer="useWindowAsScrollContainer"
        @update:list="updatedList"
        helperClass="tasks-list">

        <TaskItem
            class="task-item" v-for="(task, i) in data"
            :key="i"
            :task="task"
            :index="i"
            @editor-shown="itemOnEditMode = true"
            @editor-hidden="itemOnEditMode = false"
            @status-updated="$emit('status-updated', $event)"
        />
    </SlickList>
</template>
<script>
import TaskItem from '@/Pages/Task/Components/TaskItem.vue'
import { SlickList } from 'vue-slicksort';

export default {
    components: {
        TaskItem,
        SlickList
    },

    props: {
        data: {
            type:Array,
            required: true
        },
        useWindowAsScrollContainer: {
            type: Boolean,
            default: false
        }
    },

    data() {
        return {
            itemOnEditMode: false
        }
    },
    methods: {
        updatedList(list) {
            let ids = list.map(item => {
                return item.id
            });

            axios.post(route('tasks.sort'), { ids });
        }
    }
}
</script>
<style lang="postcss" scoped>
.tasks-list {
    @apply shadow;
}

</style>
<style lang="postcss">
.task-list-disabled > .task-item > .task-item-handle  {
    @apply invisible;
}
</style>
