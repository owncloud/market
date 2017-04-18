import 'babel-polyfill';
require('./styles/theme.scss');

// -------------------------------------------------------- Uikit components ---
import UIkit from 'uikit';
import Icons from 'uikit/dist/js/uikit-icons';

UIkit.use(Icons);

// ------------------------------------------------------------- Vue plugins ---

import Vue       from 'vue'
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import GetTextPlugin from 'vue-gettext'
import translations from '../l10n/translations.json'

Vue.use(GetTextPlugin, {translations: translations})
Vue.config.language = OC.getLocale()

// --------------------------------------------------------------- Vue setup ---

import App     from './App.vue'
import Details from './components/Details.vue'
import List    from './components/List.vue'

// Store
import { store } from './store'

// Routing
const routes = [
	{
		path: '/app/:id',
		component: Details
	}, {
		path: '/',
		component: List
	}
];

const router = new VueRouter({
	routes
});

// The App itself
const MarketApp = new Vue({
	router,
	store,
	render: h => h(App)
});

// --------------------------------------------------- Global Vue components ---

import StarRating from './components/Rating.vue';
import NavMain    from './components/Nav-Main.vue';

// Global components
Vue.component('star-rating', StarRating);
Vue.component('nav-main', NavMain);

// Need to wait for window to load
window.onload = () => MarketApp.$mount('.app-market');
