<template>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Delete {{modalType}}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this {{modalType}}?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button name="deleteFile" @click="deleteItem" type="button" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {inject} from "vue";

export default {
    setup(){
        const dbFiles = inject("dbFiles");
        const dbDirectories = inject("dbDirectories");
        const openFolderId = inject("openFolderId");

        return {
            dbFiles,
            dbDirectories,
            openFolderId,
        }
    },
    data() {
        return {
            modalType: '',
        }
    },
    created() {
        // From: folderRow.vue, fileRow.vue
        this.$customEvent.on('deleteModal', data => {
            this.deleteModal(data.modalType, data.index);
        });

    },
    methods: {
        deleteModal(modalType, index){
            this.modalType = modalType;
            this.modalIndex = index;
        },
        deleteItem(){
            const items = this.modalType === 'file' ? this.dbFiles : this.dbDirectories;
            let deleteItemId = "";
            let deleteItemName = "";
            if (this.modalType === 'file') {
                deleteItemId = items[this.modalIndex].file_id;
                deleteItemName = items[this.modalIndex].file_name;
            } else {
                deleteItemId = items[this.modalIndex].dir_id;
                deleteItemName = items[this.modalIndex].dir_name;
            }
            const url = '/delete-' + this.modalType + '/' + deleteItemId;

            axios
            .delete(url)
            .then((response) => {
                this.$toast.open({
                    message: "A '" + deleteItemName + "' nevű elem sikeresen törölve lett.",
                    type: "success",
                    duration: 5000,
                    dismissible: true,
                });
                // To: App.vue
                this.$customEvent.emit('openFolder', {'openFolderId': this.openFolderId});
            })
            .catch(error => console.log(error));
        },
    }
}
</script>

<style scoped>

</style>