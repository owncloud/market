import 'babel-polyfill';

// ------------------------------------------------------------------------------------------------ Uikit components ---
import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';

// loads the Icon plugin
UIkit.use(Icons);

import Vue from 'vue'

// -------------------------------------------------------------------------------------------------- App components ---
import App from './App.vue'

require('./styles/theme.scss');

window.onload = function () {

	new Vue({
		el: '#app',
		render: h => h(App)
	})
}