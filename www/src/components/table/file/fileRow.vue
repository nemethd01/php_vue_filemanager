<template>
    <td>
        <div class="w-25 m-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
            </svg>
        </div>
    </td>
    <td>
        <div class="fileNameDiv">
            {{ file.file_name }}
        </div>
    </td>
    <td>{{file.creation_date}}</td>
    <td>{{sizeType.size}} {{sizeType.unit}}</td>
    <td class="text-center">
         <download
            :index="index"
        />
    </td>
    <td>
        <button @click="deleteModal('file', index)" class="btn btn-sm btn-danger d-block m-auto" data-bs-toggle="modal" data-bs-target="#deleteModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
            </svg>
        </button>
    </td>
</template>

<script>
import {inject} from "vue";
import download from "../download/download.vue";

export default {
    setup(){
        const dbFiles = inject('dbFiles');

        return {
            dbFiles,
        }

    },
    props: {
        index: {
            type: Number,
            default: null
        },
        file: {
            type: Object,
            default: {},
        }
    },
    components: {download},
    data() {
        return {
        }
    },
    computed: {
        sizeType(){
            const excNum = 1024;
            const formattedFileSize = {'size': null, 'unit': null};
            if (this.file.file_size < excNum) {
                formattedFileSize.size = this.file.file_size;
                formattedFileSize.unit = 'B';
            } else if (this.file.file_size < Math.pow(excNum, 2)) {
                formattedFileSize.size = this.getFileSize(this.file.file_size, 1);
                formattedFileSize.unit = 'KB';
            } else if (this.file.file_size < Math.pow(excNum, 3)) {
                formattedFileSize.size = this.getFileSize(this.file.file_size, 2);
                formattedFileSize.unit = 'MB';
            } else {
                formattedFileSize.size = this.getFileSize(this.file.file_size, 3);
                formattedFileSize.unit = 'GB';
            }
            return formattedFileSize;
        }
    },
    methods: {
        getFileSize(fileSize, exponentNumber) {
            const excNum = Math.pow(1024, exponentNumber);
            if (fileSize % excNum === 0) {
                fileSize /= excNum;
                return fileSize.toFixed(0);
            } else {
                fileSize /= excNum;
                return fileSize.toFixed(2);
            }
        },
        deleteModal(modalType, index){
            // To: deleteModal.vue
            this.$customEvent.emit('deleteModal', {'modalType': modalType, 'index': index});
        },
    },
}
</script>

<style scoped>

</style>