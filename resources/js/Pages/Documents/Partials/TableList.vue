<script setup>
import { ref } from 'vue';
import TableItem from './TableItem.vue';
import TableHeader from './TableHeader.vue';
import UpdateDialog from './UpdateDialog.vue';

const props = defineProps({
    list: Object,
    links: String,
    filters: Object,
    archived: Boolean,
    currentUserId: Number,
    canDeleteAnyDocuments: Boolean,
    canDeleteOwnDocuments: Boolean,
    canArchiveAnyDocuments: Boolean,
    canArchiveOwnDocuments: Boolean,
    canRestoreAnyDocuments: Boolean,
    canRestoreOwnDocuments: Boolean,
    loadUser: {
        type: Boolean,
        default: true,
    },
});

defineEmits(['create'])

const showUpdateDialog = ref(false)

const updatingDocument = ref({})

const handleUpdate = (document) => {
    updatingDocument.value = document
    showUpdateDialog.value = true
}
</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="mx-auto max-w-screen-2xl ">

            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <TableHeader :filters="filters" :archived="archived" @create="$emit('create')" />

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 font-bold">
                            <tr>
                                <th scope="col" class="px-4 py-3">Datum</th>
                                <th v-if="$page.props.isAdminOrSuperAdmin" scope="col" class="px-4 py-3">Stranka</th>
                                <th scope="col" class="px-4 py-3">Podjetje</th>
                                <th scope="col" class="px-4 py-3">Leto</th>
                                <th scope="col" class="px-4 py-3">Mapa</th>
                                <th scope="col" class="px-4 py-3">Datoteka</th>
                                <th scope="col" class="px-4 py-3" v-if="$page.props.isAdminOrSuperAdmin">Obdelan</th>
                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Operacije</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="[&>*:nth-child(even)]:bg-gray-50 dark:[&>*:nth-child(even)]:bg-gray-700">
                            <TableItem
                                v-for="item, key in list.data"
                                :key="key"
                                :item="item"
                                :load-user="loadUser"
                                :archived="archived"
                                :current-user-id="currentUserId"
                                :can-delete-any-documents="canDeleteAnyDocuments"
                                :can-delete-own-documents="canDeleteOwnDocuments"
                                :can-archive-any-documents="canArchiveAnyDocuments"
                                :can-archive-own-documents="canArchiveOwnDocuments"
                                :can-restore-any-documents="canRestoreAnyDocuments"
                                :can-restore-own-documents="canRestoreOwnDocuments"
                                @update="handleUpdate"
                            />
                        </tbody>
                    </table>
                </div>
                <div v-html="links"></div>
            </div>
        </div>
        <!-- <UpdateDialog :show="showUpdateDialog" :document="updatingDocument"  @close="showUpdateDialog = false" /> -->
    </section>
</template>

