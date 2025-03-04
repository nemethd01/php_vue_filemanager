<template>
    <div class="modal fade" id="uploadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Upload file</h1>
                </div>
                <div class="modal-body">
                    Your file is uploading...

                    <!--                    Bootstrap progress bar-->
                    <div class="progress mb-4" role="progressbar" aria-label="Example with label" aria-valuenow="{{uploadProgress}}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" :style="'width: ' + uploadProgress + '%'">{{uploadProgress + '%'}}</div>
                    </div>

                    <!--                    unique progress bar-->
                    <div class="progressBar m-auto mb-3">
                        <div class="progressBarInside" :style="{ width: uploadProgress + '%' }">
                            <span class="uploadBarPercent">{{uploadProgress + '%'}}</span>
                        </div>
                    </div>

                    <!--                    fragment progress bar-->
                    <div class="progressBarFrag m-auto mb-3">
                        <div class="progressBarFragInside">
                            <!--                            <span class="progressBarFragPercent">{{ uploadProgress + '%' }}</span>-->
                            <div v-for="i in 50" :key="i"
                                 :class="{ 'progressSquare': i <= uploadProgress / 2, 'emptySquare': i > uploadProgress / 2 }"></div>
                        </div>
                    </div>

                    <!--                    circle progress bar-->
                    <div class="progressCircle m-auto" :data-progress="uploadProgress"
                         :style="'--progress:  ' + ((360 / 100) * uploadProgress) + 'deg'">
                        {{ uploadProgress + '%' }}
                    </div>

                </div>
                <div class="modal-footer">
                    <button v-if="showFinishButton === true" class="btn btn-primary" data-bs-dismiss="modal">Kész</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {inject} from "vue";

export default {
    setup() {
        const uploadProgress = inject('uploadProgress');
        const showFinishButton = inject('showFinishButton');

        return {
            uploadProgress,
            showFinishButton,
        };
    },
    data() {
        return {}
    }
}
</script>

<style scoped>

</style>