import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// If you plan to use Laravel Echo + websockets, you can add that setup here.
