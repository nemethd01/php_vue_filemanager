<template>
    <td class="tableFooter text-center" colspan="6">
        <div class="row">
            <div class="col">
                Összesen: {{ allDirNumber }} mappa és {{allFileNumber}} file.
            </div>
        </div>
    </td>
</template>

<script>
import {inject} from "vue";

export default {
    setup(){
        const dbFiles = inject('dbFiles');
        const dbDirectories = inject('dbDirectories');

        return {
            dbFiles,
            dbDirectories,
        }
    },
    data() {
        return {
            allDirNumber: 0,
            allFileNumber: 0,
        }
    },
    watch: {
        dbDirectories: {
            handler(newValue){
                this.allDirNumber = newValue.length;
            }, immediate: true
        },
        dbFiles: {
            handler(newValue) {
                this.allFileNumber = newValue.length;
            }, immediate: true
        },
    }
}
</script>

<style scoped>

</style>