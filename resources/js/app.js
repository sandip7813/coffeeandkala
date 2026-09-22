import './site.js';
import './hero-banner.js';
import './home-carousels.js';

import { initFrontendFancybox } from './frontend-fancybox.js';
import { initSiteSearch } from './search.js';

document.addEventListener('DOMContentLoaded', initFrontendFancybox);
document.addEventListener('DOMContentLoaded', initSiteSearch);
