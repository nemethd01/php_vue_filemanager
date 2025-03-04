<template>
    <div class="container pt-5 pb-5 h-100">
        <div class="row">
            <!--            uploader-->
            <div class="col">
                <file-upload/>
            </div>

            <!--            create new folder-->
            <div class="col">
                <create-new-folder
                    @createdFolder = "sortFilesBy(this.filterField)"
                />
            </div>
        </div>
        <div class="mt-3">
            <table v-if="dbFiles != '' || dbDirectories != ''" class="table table-striped table-bordered">
                <thead>
                    <header-row
                        :rotateFilterIcon="rotateFilterIcon"
                        :filterDirectionDown="filterDirectionDown"
                    />
                </thead>
                <tbody>
                    <folder-row/>
                    <tr v-for="(file, index) in dbFiles">
                        <file-row
                            :index="index"
                            :file="file"
                        />
                    </tr>
                <!--                table footer-->
                <tr>
                    <footer-row/>
                </tr>
                </tbody>
            </table>

            <!--            Delete modal-->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <delete-modal/>
            </div>

            <!--            If not file & dir-->
            <table v-if="dbFiles == '' && dbDirectories == ''" class="table table-striped table-bordered">
                <empty-table/>
            </table>
        </div>
    </div>
</template>

<script>

import axios from "axios";
import fileUpload from "./components/upload/fileUpload.vue";
import createNewFolder from "./components/create_folder/createNewFolder.vue";
import headerRow from "./components/table/header/headerRow.vue";
import footerRow from "./components/table/footer/footerRow.vue";
import emptyTable from "./components/table/emptyTable.vue";
import folderRow from "./components/table/folder/folderRow.vue";
import deleteModal from "./components/table/delete/deleteModal.vue";
import fileRow from "./components/table/file/fileRow.vue";
import {provide, ref} from "vue";

export default {
    setup(){
        const dbFiles = ref([]);
        const dbDirectories = ref([]);
        const openFolderId = ref('');
        const createFolderName = ref('');
        const actualDirName = ref({});
        const isRootDir = ref(false);

        provide('dbFiles', dbFiles);
        provide('dbDirectories', dbDirectories);
        provide('openFolderId', openFolderId);
        provide('createFolderName', createFolderName);
        provide('actualDirName', actualDirName);
        provide('isRootDir', isRootDir);

        return {
            dbFiles,
            dbDirectories,
            openFolderId,
            createFolderName,
            actualDirName,
            isRootDir,
        };

    },
    components: {
        fileUpload,
        createNewFolder,
        headerRow,
        footerRow,
        emptyTable,
        folderRow,
        deleteModal,
        fileRow,
    },
    data(){
        return {
            dirPath: {},
            fileData: [],
            directories: [],
            rotateFilterIcon: {
                fileName: false,
                creationDate: false,
                origFileSize: false,
            },
            filterDirectionDown: true,
            filterField: null,
            modalIndex: null,
            targetPath: {},
        }
    },
    created() {
        // From: fileUpload.vue, headerRow.vue, folderRow.vue, deleteModal.vue
        this.$customEvent.on('openFolder', data => {
            this.openFolder(data.openFolderId);
        });
        // From: headerRow.vue
        this.$customEvent.on('backToRoot', () => {
            this.openFolder();
        });
        // From: headerRow.vue
        this.$customEvent.on('sortFilesBy', data => {
            this.sortFilesBy(data.filterBy);
        })
    },
    mounted() {
        this.openFolder();
    },
    watch: {
        dirPath: {
            handler: function (newValue) {
                this.targetPath = newValue;
            }
        },
    },
    methods: {
        getContent(response) {
            this.dirPath = response.data.dirPath;
            this.dbFiles = response.data.files;
            this.dbDirectories = response.data.directories;
            this.isRootDir = response.data.isRootDir;
            if (response.data.actualDirName) {
                this.actualDirName[this.openFolderId] = response.data.actualDirName;
            }
        },
        sortFilesBy(filterBy) {
            this.filterField = filterBy;
            this.filterDirectionDown = !this.filterDirectionDown;
            this.rotateFilterIcon[filterBy] = !this.rotateFilterIcon[filterBy];
            this.openFolder(this.openFolderId);
        },
        openFolder(folderId = null){
            this.openFolderId = folderId;
            const url = folderId ? '/folder' + '/' + folderId : '/folder';

            if (!folderId) {
                this.actualDirName = {};
            }
            let params = {}
            if (this.filterField) {
                params = {
                    filterBy: this.filterField,
                    sort: this.filterDirectionDown == false ? 'ASC' : 'DESC',
                }
            }

            axios
            .get(url, {
                params: params,
            })
            .then((response) => {
                this.getContent(response);
            })
            .catch(error => console.log(error));
        },
    },
}

</script>

<style scoped>

</style>
