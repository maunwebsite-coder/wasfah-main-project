import './bootstrap';
import './header';
import './loading-fix';
import { registerSW } from 'virtual:pwa-register';

registerSW({
    immediate: true,
});
