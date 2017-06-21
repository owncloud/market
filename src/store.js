import Vue from 'vue';
import Vuex from 'vuex';
import Axios from 'axios';
import _ from 'underscore';

Vue.use(Vuex);

const state = {
	categories: {
		loading: false,
		failed: false,
		records: {}
	},

	applications: {
		loading: false,
		failed: false,
		records: {}
	},

	bundles: {
		loading: false,
		failed: false,
		records: {}
	},

	apikey : {
		key : null,
		changeable : false
	},

	processing: []
}

// Retrieve computed values from state.
const getters = {
	categories (state) {
		return state.categories.records;
	},
	category: (state) => (id) => {
		return _.find(state.categories.records, function (category) {
			return category.id == id;
		});
	},
	applications: (state) => (category) => {
		if (category === undefined) {
			return state.applications.records;
		}

		return _.filter(state.applications.records, function (application) {
			return _.contains(application.categories, category);
		});
	},
	bundles: (state) => {
		return state.bundles.records;
	},
	application: (state) => (id) => {
		return _.find(state.applications.records, function (application) {
			return application.id == id;
		});
	},
	updateList: (state) => {
		return _.filter(state.applications.records, function (application) {
			return application.updateInfo != false;
		});
	},
	apikey (state) {
		return state.apikey;
	}
};

// Manipulate from the current state.
const mutations = {
	LOADING_APPLICATIONS (state) {
		_.extend(state['applications'], {
			loading: true,
			failed: false
		})
	},
	FAILED_APPLICATIONS (state) {
		_.extend(state['applications'], {
			loading: false,
			failed: true,
			records: {}
		})
	},
	FINISH_APPLICATIONS (state) {
		_.extend(state['applications'], {
			loading: false,
			failed: false
		})
	},
	SET_APPLICATIONS (state, content) {
		_.extend(state['applications'], {
			records: content
		})
	},
	SET_BUNDLES (state, content) {
		_.extend(state['bundles'], {
			records: content
		})
	},
	LOADING_CATEGORIES (state) {
		_.extend(state['categories'], {
			loading: true,
			failed: false,
			records: {}
		})
	},
	FAILED_CATEGORIES (state) {
		_.extend(state['categories'], {
			loading: false,
			failed: true,
			records: {}
		})
	},
	FINISH_CATEGORIES (state) {
		_.extend(state['categories'], {
			loading: false,
			failed: false
		})
	},
	SET_CATEGORIES (state, content) {
		_.extend(state['categories'], {
			records: content
		})
	},
	START_PROCESSING (state, id) {
		state['processing'].push(id)
	},
	FINISH_PROCESSING (state, id) {
		state['processing'] = _.without(state['processing'], id)
	},
	SET_APIKEY (state, key) {
		state['apikey'] = key
	},
};

// Request content from the remote API.
const actions = {
	PROCESS_APPLICATION (context, payload) {
		let id    = payload[0];
		let route = payload[1];

		context.commit('START_PROCESSING', id)

		Axios.post(OC.generateUrl('/apps/market/apps/{id}/' + route, {id}),
			{}, {
				headers: {
					requesttoken: OC.requestToken
				}
			}
		).then((response) => {
			UIkit.notification(response.data.message, {status:'success', pos: 'bottom-right'})
			context.commit('FINISH_PROCESSING', id)
			context.dispatch('FETCH_APPLICATIONS')
		}).catch((error) => {
			UIkit.notification(error.response.data.message, {status:'danger', pos: 'bottom-right'});
			context.commit('FINISH_PROCESSING', id);
		})
	},
	FETCH_APPLICATIONS (context) {
		context.commit('LOADING_APPLICATIONS')

		Axios.get(OC.generateUrl('/apps/market/apps'))
			.then((response) => {
				context.commit('SET_APPLICATIONS', response.data)
				context.commit('FINISH_APPLICATIONS')
			})
			.catch((error) => {
				UIkit.notification(error.response.data.message, {status:'danger', pos: 'bottom-right'})
				context.commit('FAILED_APPLICATIONS')
			});
	},
	FETCH_BUNDLES (context) {
		context.commit('LOADING_APPLICATIONS')

		Axios.get(OC.generateUrl('/apps/market/bundles'))
			.then((response) => {
				context.commit('SET_BUNDLES', response.data)
				context.commit('FINISH_APPLICATIONS')
			})
			.catch((error) => {
				UIkit.notification(error.response.data.message, {status:'danger', pos: 'bottom-right'})
				context.commit('FAILED_APPLICATIONS')
			});
	},
	FETCH_CATEGORIES (context) {
		context.commit('LOADING_CATEGORIES')

		Axios.get(OC.generateUrl('/apps/market/categories'))
			.then((response) => {
				context.commit('SET_CATEGORIES', response.data)
				context.commit('FINISH_CATEGORIES')
			})
			.catch((error) => {
				UIkit.notification(error.response.data.message, {status:'danger', pos: 'bottom-right'})
				context.commit('FAILED_CATEGORIES')
			});
	},
	FETCH_APIKEY (context) {
		Axios.get(OC.generateUrl('/apps/market/apikey'))
			.then((response) => {
				context.commit('SET_APIKEY', response.data)
			})
			.catch((error) => {
				UIkit.notification(error.response.data.message, {status:'danger', pos: 'bottom-right'})
			});
	},
	WRITE_APIKEY (context, payload) {
		let key = payload;

		Axios.put(OC.generateUrl('/apps/market/apikey'),
			{
				'apiKey' : key
			}, {
				headers: {
					requesttoken: OC.requestToken
				}
			}
		).then((response) => {
			UIkit.notification(response.data.message, {status:'success', pos: 'bottom-right'});
		}).catch((error) => {
			UIkit.notification(error.response.data.message, {status:'danger', pos: 'bottom-right'});
		})
	},
}

export default new Vuex.Store({
	state,
	getters,
	mutations,
	actions
})
