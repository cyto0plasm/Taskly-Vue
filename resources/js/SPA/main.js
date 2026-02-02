import {createApp} from 'vue';
import taskList from './tasks/TaskList.vue';
import taskDetails from './tasks/TaskDetails.vue';
import Fab from './components/Fab.vue';
import {createPinia} from 'pinia';
import globalUi from './globalUi.vue';

// Create ONE shared Pinia instance
const pinia = createPinia();

const taskListApp= createApp(taskList);
taskListApp.use(pinia)
taskListApp.mount('#spataskList');

const taskDetailsApp=createApp(taskDetails);
taskDetailsApp.use(pinia)
taskDetailsApp.mount('#spataskDetails');

const app = createApp(globalUi);
app.use(pinia);
app.mount('#global-ui');
