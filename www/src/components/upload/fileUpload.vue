<template>
    <div>
        <input
            type="file"
            name="file"
            ref="file"
            @change="fileChange($event)"
        >
        <button
            class="button-up mx-2"
            :disabled="stopUpload"
            @click="isUploadFileExist()"
        >
            Upload
        </button>
    </div>

    <!--    Upload modal -->
    <upload-modal/>

    <!--    upload modal, existing file -->
    <file-exist-modal/>

</template>

<script>
import {inject, provide, ref} from "vue";
import axios from "axios";
import uploadModal from "./modals/uploadModal.vue";
import fileExistModal from "./modals/fileExistModal.vue";

export default {
    setup(){
        const dbFiles = inject('dbFiles');
        const openFolderId = inject('openFolderId');

        const uploadProgress = ref(0);
        const showFinishButton = ref(false);
        provide('uploadProgress', uploadProgress);
        provide('showFinishButton', showFinishButton);

        return {
            dbFiles,
            openFolderId,
            uploadProgress,
            showFinishButton,
        };

    },
    components: {fileExistModal, uploadModal},

    data() {
        return {
            uploadFileData: '',
            stopUpload: false,
            actuallyCount: 0,
        }

    },
    created() {
        // From: fileExistModal
        this.$customEvent.on('reUpload', () => {
            this.uploader()
        });

    },
    methods: {
        fileChange(event){
            this.uploadFileData = event.target.files[0];
        },
        uploadErrorMessage() {
            this.$toast.open({
                message: "A feltölteni kívánt file mérete meghaladja a maximumot!",
                type: "error",
                duration: 5000,
                dismissible: true
            })
        },
        isUploadFileExist(){
            if (!this.uploadFileData) {
                this.$toast.open({
                    message: "Válassza ki a feltöltendő file-t!",
                    type: "warning",
                    duration: 5000,
                    dismissible: true
                })
                return;
            }

            // check if the file exist
            /*let error = [];
            this.showFinishButton = false;
            this.dbFiles.forEach(file => {
                if (this.uploadFileData.name === file.generated_name){
                    error.push(file.generated_name);
                }
            })

            if (error.length === 0) {
                this.uploader();
            } else {
                const uploadModal = new bootstrap.Modal('#uploadFileExistModal', {
                    keyboard: false,
                    backdrop: 'static',
                });
                uploadModal.show();
            }*/
            this.uploader();

        },
        uploader() {
            this.uploadHelper('#uploadModal');
        },
        uploadHelper(modalId) {
            const modalName = new bootstrap.Modal(modalId, {
                keyboard: false,
                backdrop: 'static',
            });
            this.uploadFileEvent(modalName);
        },
        uploadFileEvent(modalName) {
            modalName._element.removeEventListener('shown.bs.modal', this.uploadFile);
            modalName._element.addEventListener('shown.bs.modal', this.uploadFile);
            modalName.show();
        },
        openFolder(){
            this.$customEvent.emit('openFolder');
        },
        async uploadFile() {
            let file = this.uploadFileData;
            this.actuallyCount = 0; // le kell nullázni, különben mindig hozzá számolja az előzőt

            // const CHUNK_SIZE = 1024 * 1024; // 1 MB
            let CHUNK_SIZE = 0;
            if (this.uploadFileData.size <= 52428800) {
                // 52428800 - 50 MB
                CHUNK_SIZE = 1024 * 1024; // 1 MB
            } else {
                CHUNK_SIZE = 1024 * 1024 * 1.5;
            }
            const chunks = Math.ceil(file.size / CHUNK_SIZE);
            const promises = [];

            for (let i = 0; i < chunks; i++) {
                const start = i * CHUNK_SIZE;
                const end = (i + 1) * CHUNK_SIZE;
                const chunk = file.slice(start, end);
                promises.push(this.uploadChunk(chunk, i + 1, chunks, file.name));
            }

            try {
                await Promise.all(promises)
                    .then((response) => {
                        axios
                            .post('/finish-upload', {
                                finishUpload: true,
                                totalChunks: chunks,
                                origFileName: file.name,
                                openFolderId: this.openFolderId,
                            },{
                                headers: {
                                    'content-type': 'multipart/form-data',
                                }
                            })
                            .then((response) => {
                                console.log("Kész");
                                this.uploadFileData = '';
                                this.$toast.open({
                                    message: "A '" + file.name + "' nevű file feltöltése sikeres!",
                                    type: "success",
                                    duration: 5000,
                                    dismissible: true,
                                });
                                // To: App.vue
                                this.$customEvent.emit('openFolder', {'openFolderId': this.openFolderId});
                                this.$refs.file.value = null;
                                this.showFinishButton = true;
                            })
                            .catch(error => console.log(error));
                    })
                console.log('All chunks uploaded successfully');
            } catch (error) {
                console.error('Error uploading chunks:', error);
                this.$toast.open({
                    message: "A '" + file.name + "' nevű file feltöltése sikertelen!",
                    type: "error",
                    duration: 5000,
                    dismissible: true
                });
            }
        },
        async uploadChunk(chunk, chunkNumber, totalChunks, fileName) {
            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('origFileName', fileName)
            formData.append('chunkNumber', chunkNumber);
            formData.append('totalChunks', totalChunks);

            try {
                const response = await axios.post('/upload-file-chunks', formData);
                console.log(`Chunk ${chunkNumber} uploaded successfully`);
                this.actuallyCount ++;
                this.uploadProgress = Math.round((this.actuallyCount / totalChunks) * 100);
                return response.data; // Return any response from the server if needed
            } catch (error) {
                console.error(`Error uploading chunk ${chunkNumber}:`, error);
                throw error; // Propagate the error for handling in the caller
            }
        },

    },
    watch: {
        uploadFileData: {
            handler(newValue){
                if (newValue) {
                    if (this.uploadFileData.size > 1610612736) {
                        this.stopUpload = true;
                        // 5242880 - 5mb;
                        // 1610612736 1,5 GB;
                        this.uploadErrorMessage();
                    }
                }
            }
        },

    }
}
</script>

<style scoped>

</style>