<template>
    <div>
        <div class="row">
            <div class="col">
                <input
                    name="createFolder"
                    placeholder="Folder name"
                    class="createFolderInput form-control"
                    type="text"
                    v-model="createFolderName"
                >
            </div>
            <div class="col">
                <button name="createFolder" @click="createFolder" class="button-add" type="submit">Create folder</button>
            </div>
        </div>
    </div>
</template>

<script>
import {inject} from "vue";
import axios from "axios";

export default {
    emits: ["createdFolder"],
    setup(){
        const openFolderId = inject('openFolderId');
        return{
            openFolderId,
        }
    },
    data() {
        return {
            createFolderName: '',
        }
    },
    methods: {
        createFolder() {
            if (!this.createFolderName) {
                this.$toast.open({
                    message: 'Adja meg a létrehozandó mappa nevét!',
                    type: "warning",
                    duration: 5000,
                    dismissible: true
                });
                return;
            }

            axios
            .get('/create-folder', {
                params: {
                    folderId: this.openFolderId,
                    createFolder: this.createFolderName,
                }
            })
            .then((response) => {
                this.$emit('createdFolder');
                this.$toast.open({
                    message: "'" + this.createFolderName + "' nevű mappa sikeresen hozzáadva.",
                    type: "success",
                    duration: 5000,
                    dismissible: true,
                })
                this.createFolderName = "";
            })
            .catch(error => console.log(error));

        },
    }
}
</script>

<style scoped>

</style>