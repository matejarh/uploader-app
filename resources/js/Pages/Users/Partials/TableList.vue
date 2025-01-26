<script setup>
import { ref } from 'vue';
import TableItem from './TableItem.vue';
import TableHeader from './TableHeader.vue';
import UpdateDialog from './UpdateDialog.vue';

const props = defineProps({
    list: Object,
    links: String,
    filters: Object,
});

defineEmits(['create'])

const showUpdateDialog = ref(false)

const updatingDocument = ref({})

const handleShow = (document) => {
    updatingDocument.value = document
    showUpdateDialog.value = true
}
</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-2xl ">
            <!-- Start coding here -->
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <TableHeader :filters="filters" @create="$emit('create')" />

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-bold">
                            <tr>
                                <th scope="col" class="px-4 py-3">Registriran</th>
                                <th scope="col" class="px-4 py-3">Stranka</th>
                                <th scope="col" class="px-4 py-3">Email</th>

                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Operacije</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&>*:nth-child(even)]:bg-gray-50 dark:[&>*:nth-child(even)]:bg-gray-700">
                            <TableItem v-for="item, key in list.data" :key="key" :item="item"
                                @show="handleShow" />
                        </tbody>
                    </table>
                </div>
                <div v-html="links"></div>
            </div>
        </div>
        <!-- <UpdateDialog :show="showUpdateDialog" :document="updatingDocument"  @close="showUpdateDialog = false" /> -->
    </section>
</template>

