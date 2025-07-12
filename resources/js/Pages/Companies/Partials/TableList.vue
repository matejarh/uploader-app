<script setup>
import { ref } from 'vue';
import TableItem from './TableItem.vue';
import TableHeader from './TableHeader.vue';
import UpdateDialog from './UpdateDialog.vue';

const props = defineProps({
    list: Object,
    links: String,
    filters: Object,
    loadUser: {
        type: Boolean,
        default: true,
    },
});

defineEmits(['create'])

const showUpdateDialog = ref(false)

const updatingCompany = ref({})

const handleUpdate = (company) => {
    updatingCompany.value = company
    showUpdateDialog.value = true
}
</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-2xl ">

            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <TableHeader :filters="filters" @create="$emit('create')" v-if="loadUser" />

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-bold">
                            <tr>
                                <th v-if="loadUser" scope="col" class="px-4 py-3">Stranka</th>
                                <th v-else scope="col" class="px-4 py-3">Podjetje</th>
                                <th scope="col" class="px-4 py-3">Naslov podjetja</th>
                                <!-- <th scope="col" class="px-4 py-3">Leto</th>
                                <th scope="col" class="px-4 py-3">Mapa</th>
                                <th scope="col" class="px-4 py-3">Datoteka</th> -->
                                <!-- <th scope="col" class="px-4 py-3" v-if="$page.props.isAdminOrSuperAdmin">Obdelan</th> -->

                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Operacije</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&>*:nth-child(even)]:bg-gray-50 dark:[&>*:nth-child(even)]:bg-gray-700">
                            <TableItem v-for="item, key in list.data" :key="key" :item="item" :load-user="loadUser"
                                @update="handleUpdate" />
                        </tbody>
                    </table>
                </div>
                <div v-html="links"></div>
            </div>
        </div>
        <UpdateDialog :show="showUpdateDialog" :company="updatingCompany"  @close="showUpdateDialog = false" />
    </section>
</template>

