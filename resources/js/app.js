import './bootstrap';
import './header';
import './loading-fix';
import './content-localization';
import { registerSW } from 'virtual:pwa-register';

registerSW({
    immediate: true,
});
