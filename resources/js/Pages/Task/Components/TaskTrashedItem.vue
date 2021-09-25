<template>
    <div class="py-3 flex">
        <div class="w-full">
            <span v-if="task.parent" class="mt-1 text-xs flex align-middle text-gray-500">
                <SubTaskIcon class="mr-1"/>
                {{ task.parent.content }}
            </span>
            <p class="py-1">{{ task.content }}</p>
            <span v-if="task.subtasks_count > 0" class="mt-1 text-xs flex text-gray-500">
                Sub-tasks ({{ task.subtasks_count }})
            </span>
        </div>
        <div class="action">
            <a class="action-btn text-red-400 hover:text-red-700" @click="destroy">Destroy</a>
            <a class="action-btn text-gray-500 hover:text-gray-800" @click="restore">{{ restoring ? 'Restoring...' : 'Restore' }}</a>
        </div>
    </div>
</template>
<script>
import { Link } from '@inertiajs/inertia-vue3';
import get from 'lodash/get';
import throttle from 'lodash/throttle';
import SubTaskIcon from '@/Pages/Task/Components/SubTaskIcon.vue';

export default {
    props: {
        task: {
            type: Object,
            required: true
        }
    },
    components: {
        Link,
        SubTaskIcon
    },

    data() {
        return {
            restoring: false
        }
    },

    methods: {
        destroy() {
            this.$swal({
                title: 'Are you sure?',
                text: this.task.subtasks_count === 0 ? "You won't be able to revert this!" : "Sub-tasks will also be deleted. You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Continue',
                customClass: {
                    confirmButton: 'dialog-btn-danger',
                    cancelButton: 'dialog-btn-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(route('tasks.destroy', this.task.id));
                }
            })
        },
        restore: throttle(async function() {
            try {
                if (this.restoring) {
                    return;
                }

                this.restoring = true
                await axios.post(route('tasks.restore', this.task.id));
                this.$inertia.reload(route('tasks.trashed'), {
                    onSuccess() {
                        this.restoring = false
                    }
                });
            } catch (e) {
                this.$swal({
                    title: 'Process Failed',
                    text: get(e, 'response.data.message', 'Something went wrong!'),
                    icon: 'error',
                    confirmButtonText: 'Close',
                    customClass: {
                        confirmButton: 'dialog-btn-primary'
                    }
                })
                this.restoring = false
            }
        }, 250)
    }
}
</script>
<style lang="postcss" scoped>
.action :not(:first-child) {
    @apply ml-1;
}

.action-btn {
    @apply px-1 py-1 cursor-pointer;

}
</style>
