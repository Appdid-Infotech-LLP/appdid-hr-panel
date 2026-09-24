import $ from 'jquery';

window.$ = window.jQuery = $;

// select2's UMD build exports an uninvoked factory under Vite's CJS
// interop instead of self-attaching to jQuery, so it has to be called
// manually with the jQuery instance it should extend.
import select2 from 'select2';
select2(window, $);
import 'select2/dist/css/select2.min.css';

import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.flatpickr = flatpickr;
