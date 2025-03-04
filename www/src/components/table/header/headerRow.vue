<template>
    <tr class="tableFolderPath">
        <td colspan="6">
            <div class="row">
                <div class="col pt-1">Path: {{ getFolderPath }}</div>
                <div class="col text-end">
                    <button v-if="isRootDir === false" @click="goBackOneLevel()" name="folderName" class="btn btn-sm">< Back</button>
                    <button v-if="isRootDir === false" @click="openFolder()" name="folderName" class="btn btn-sm"><< Back to root</button>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <th class="text-center">Type</th>
        <th>File name
            <button class="btn btn-sm" @click="sortFilesBy('file_name')" :class="{ rotate: rotateFilterIcon.file_name && !filterDirectionDown}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down" viewBox="0 0 16 16">
                    <path d="M3.204 5h9.592L8 10.481zm-.753.659 4.796 5.48a1 1 0 0 0 1.506 0l4.796-5.48c.566-.647.106-1.659-.753-1.659H3.204a1 1 0 0 0-.753 1.659"/>
                </svg>
            </button>
        </th>
        <th>Creation date
            <button class="btn btn-sm" @click="sortFilesBy('creation_date')" :class="{ rotate: rotateFilterIcon.creation_date && !filterDirectionDown }">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down" viewBox="0 0 16 16">
                    <path d="M3.204 5h9.592L8 10.481zm-.753.659 4.796 5.48a1 1 0 0 0 1.506 0l4.796-5.48c.566-.647.106-1.659-.753-1.659H3.204a1 1 0 0 0-.753 1.659"/>
                </svg>
            </button>
        </th>
        <th>File size</th>
        <th class="text-center">Download</th>
        <th class="text-center">Delete</th>
    </tr>
</template>

<script>
import {inject} from "vue";

export default {
    setup(){
        const dbDirectories = inject("dbDirectories");
        const actualDirName = inject('actualDirName');
        const isRootDir = inject('isRootDir');
        const openFolderId = inject('openFolderId');

        return {
            dbDirectories,
            actualDirName,
            isRootDir,
            openFolderId,
        }

    },
    props: {
        rotateFilterIcon: {
            type: Object,
            default: {},
        },
        filterDirectionDown: {
            type: Boolean,
            default: true,
        }
    },
    data() {
        return {}

    },
    computed: {
        getFolderPath() {
            return " / " + Object.values(this.actualDirName).join(" / ");
        },

    },
    methods: {
        openFolder(){
            // To: App.vue
            this.$customEvent.emit('backToRoot');
        },
        goBackOneLevel(){
            delete this.actualDirName[this.openFolderId];
            const folderIds = Object.keys(this.actualDirName);
            this.openPrevFolder(folderIds.pop());
        },
        openPrevFolder(openFolderId){
            // To: App.vue
            this.$customEvent.emit('openFolder', {'openFolderId': openFolderId});
        },
        sortFilesBy(filterBy){
            // To: App.vue
            this.$customEvent.emit('sortFilesBy', {'filterBy': filterBy});
        }

    }
}
</script>

<style scoped>

</style>