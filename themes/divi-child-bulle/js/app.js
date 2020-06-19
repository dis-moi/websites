
import { browserReady } from './events';
import { initExtensionInstaller } from 'bulle-extension';

browserReady(() => {
  console.info('Dismoi JS is ready');

  initExtensionInstaller();

});
