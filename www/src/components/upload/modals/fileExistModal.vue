<template>
    <div class="modal fade" id="uploadFileExistModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Azonos file név</h1>
                </div>
                <div class="modal-body">
                    Ezen a néven már létezik file ebben a mappában. Felülírja a meglévő file-t?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mégse</button>
                    <button
                        type="button"
                        class="btn btn-danger"
                        data-bs-dismiss="modal"
                        @click="reUpload()"
                    >
                        Felülír
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    setup() {

        return {}
    },
    methods: {
        reUpload(){
            // To: fileUpload
            this.$customEvent.emit('reUpload');
        }
    }
}
</script>

<style scoped>

</style>