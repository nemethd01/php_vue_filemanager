<template>
    <button @click="downloadFile(index)" class="btn btn-sm btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
        </svg>
    </button>
</template>

<script>
import axios from "axios";
import {inject} from "vue";

export default {
    setup(){
        const dbFiles = inject("dbFiles");

        return {
            dbFiles,
        }
    },
    data() {
        return {}
    },
    props: {
        index: {
            type: Number,
            default: null,
        },
    },
    methods: {
        downloadFile(index) {
            const downloadFileId = this.dbFiles[index].file_id;
            const url = '/download/' + downloadFileId;

            axios
            .get(url, {
                responseType: 'blob',
                headers: {
                    'content-type': 'multipart/form-data',
                }
            })
            .then((response) => {
                // create file link in browser's memory
                const href = URL.createObjectURL(response.data);

                // create "a" HTML element with href to file & click
                const link = document.createElement('a');
                link.href = href;
                // link.setAttribute('download', response.headers.filename);
                link.setAttribute('download', this.dbFiles[index].file_name); //or any other extension
                document.body.appendChild(link);
                link.click();

                // clean up "a" element & remove ObjectURL
                document.body.removeChild(link);
                URL.revokeObjectURL(href);
            })
            .catch(error => console.log(error));

        },
    }
}
</script>

<style scoped>

</style>