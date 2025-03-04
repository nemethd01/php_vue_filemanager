<template>
    <tr v-for="(dir, index) in dbDirectories">
        <td class="td">
            <div class="w-25 m-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder" viewBox="0 0 16 16">
                    <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a2 2 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139q.323-.119.684-.12h5.396z"/>
                </svg>
            </div>
        </td>
        <td>
            <button name="folderName" @click="openFolder(dir.dir_id)" class="btn">{{ dir.dir_name + " - " +dir.dir_id }}</button>
        </td>
        <td>{{ dir.creation_date }}</td>
        <td>-</td>
        <td>-</td>
        <td>
            <button class="btn btn-sm btn-danger d-block m-auto" data-bs-toggle="modal" data-bs-target="#deleteModal" @click="deleteModal('folder', index)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                </svg>
            </button>
        </td>
    </tr>
</template>

<script>
import {inject} from "vue";

export default {
    setup(){
        const dbDirectories = inject('dbDirectories');

        return {
            dbDirectories,
        };

    },
    data() {
        return {}

    },
    methods: {
        openFolder(openFolderId){
            // To: App.vue
            this.$customEvent.emit('openFolder', {'openFolderId': openFolderId});
        },
        deleteModal(modalType, index){
            // To: deleteModal.vue
            this.$customEvent.emit('deleteModal', {'modalType': modalType, 'index': index});
        }
    }
}
</script>

<style scoped>

</style>