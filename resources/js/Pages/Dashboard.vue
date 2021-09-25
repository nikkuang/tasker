<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <BreezeLabel for="content" value="Create new Task" />
                        <div class="flex justify-center align-middle mt-2">
                            <BreezeInput id="content" type="text" class="block w-full"  required autocomplete="username" />
                            <BreezeButton class="ml-4" >
                                Create
                            </BreezeButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeInput from '@/Components/Input.vue'
import BreezeLabel from '@/Components/Label.vue'
import BreezeButton from '@/Components/Button.vue'
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        BreezeAuthenticatedLayout,
        BreezeLabel,
        BreezeInput,
        BreezeButton,
        Head,
    },
}
</script>
