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

// TODO: Write plugin for global t() method

// --------------------------------------------------------------- Vue setup ---

import App        from './App.vue'
import Details    from './components/Details.vue'
import List       from './components/List.vue'
import UpdateList from './components/UpdateList.vue'

// Store
import store from './store'

// Routing
const routes = [
	{
		path: '/app/:id',
		component: Details,
		name: 'details'
	}, {
		path: '/by/category/:category',
		component: List,
		name: 'byCategory'
	}, {
		path: '/updates',
		component: UpdateList,
		name: 'UpdateList'
	}, {
		path: '/',
		component: List,
		name: 'index'
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

// --------------------------------------------------------------- Vue mount ---

// Need to wait for window to load
window.onload = () => MarketApp.$mount('.app-market');
