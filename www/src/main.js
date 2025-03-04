import { createApp } from 'vue';
import App from './App.vue';
import axios from 'axios'
// import router from './router/router.js'
import 'bootstrap/dist/css/bootstrap.min.css';
import * as bootstrap from 'bootstrap/dist/js/bootstrap.min.js';
// import '../dist/assets/style.css'
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import _ from 'lodash';
window._ = _;
window.axios = axios;
window.bootstrap = bootstrap;
import CustomEvent from "../src/CustomEvent.js";



const app = createApp(App);
app.config.globalProperties.$customEvent = CustomEvent;
// app.use(router);
app.use(ToastPlugin);
app.mount('#app');
// CustomEvent
