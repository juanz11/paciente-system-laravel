import { createApp } from 'vue';
import App from './App.vue';
import axios from 'axios';

// Configure axios to send CSRF token and cookies
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
} else {
  console.error('CSRF token not found');
}

// Enable sending cookies with requests
axios.defaults.withCredentials = true;

createApp(App).mount('#app');
