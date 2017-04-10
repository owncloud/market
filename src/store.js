import Vue       from 'vue';
import Vuex      from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
	state: {
		categories: {},
		apps: {},
	},
	getters : {
		apps(state) {
			// return all apps
			return state.apps;
		}
	}
});