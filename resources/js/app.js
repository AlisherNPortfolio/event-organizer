import { createApp } from 'vue';
import { Notifications } from '@kyvg/vue3-notification';


const app = createApp({
    data() {
        return {
            message: 'Tadbirlar platformasi'
        };
    }
});


app.use(Notifications);

const appElement = document.getElementById('app');
if (appElement) {
    app.mount("#app");
}
