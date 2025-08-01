import { createApp } from 'vue';
import { Notifications } from '@kyvg/vue3-notification';
import PhotoGallery from './components/PhotoGallery.vue'
import MainEventViewComponent from './components/events/MainEventViewComponent.vue';


const app = createApp({
    data() {
        return {
            message: 'Tadbirlar platformasi'
        };
    }
});


app.use(Notifications);

app.component('photo-gallery', PhotoGallery)
app.component('main-event-view-component', MainEventViewComponent);

const appElement = document.getElementById('app');
if (appElement) {
    app.mount("#app");
}
