<template>
    <form @submit.prevent="submit">
        <textarea
            class="w-full rounded border-gray-200 outline-none"
            v-model="form.content"
        />
        <div class="action">
            <BreezeButton
                :class="{ 'opacity-25': !form.isDirty || form.processing }"
                :disabled="!form.isDirty || form.processing"
                >
                {{ isEditMode ? 'Save' : 'Add task' }}
            </BreezeButton>

            <BreezeButton
                @click.prevent.stop="cancel"
                type="button"
                class="cancel-btn">
                Cancel
            </BreezeButton>
        </div>
    </form>
</template>
<script>
import BreezeButton from '@/Components/Button.vue';
import isEmpty from 'lodash/isEmpty';
import isNull from 'lodash/isNull';

export default {
    components: {
        BreezeButton
    },

    props: {
        parentTask: {
            type: Object,
            required: false,
            default: null
        },
        task: {
            type: Object,
            required: false,
            default: null
        },
        asSubtask: {
            type: Boolean,
            default: true
        }
    },

    computed: {
        isEditMode() {
            return !isEmpty(this.task) && !isNull(this.task.id)
        }
    },

    data() {
        return {
            form: this.$inertia.form({
                content: null,
                ...this.task,
                as_subtask: this.asSubtask
            })
        }
    },

    methods: {
        submit() {
            if (this.isEditMode) {
                // Edit a task
                this.form.put(this.route('tasks.update', this.task.id), {
                     onSuccess: () => {
                        this.$emit('success');
                    },
                    preserveScroll: true,
                    resetOnSuccess: true
                })

            } else {
                // When a parent was specified, treat it as adding of sub tasks.
                const route = isEmpty(this.parentTask) ?
                    this.route('tasks.store') :
                    this.route('tasks.subtasks', this.parentTask.id);

                this.form.post(route, {
                    onSuccess: () => {
                        this.form.reset();
                        this.$emit('success');
                    },
                    preserveScroll: true,
                    resetOnSuccess: false
                })
            }

        },

        cancel() {
            this.form.reset();
            this.$emit('cancel');
        }
    }
}
</script>
<style lang="postcss" scoped>
.action {
    @apply flex flex-row;
}

.action :not(:first-child) {
    @apply ml-3;
}

.cancel-btn {
    @apply text-gray-800;
    @apply border;
    @apply border-gray-400;
    @apply bg-white;
    @apply hover:bg-gray-200;
    @apply active:bg-gray-400;
}
</style>
