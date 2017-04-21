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

	installing: [],
	updating: []
}

// Retrieve computed values from state.
const getters = {
	categories: (state) => () => {
		return state.categories.records;
	},
	category: (state) => (id) => {
		return _.find(state.categories.records, function(category) {
			return category.id == id;
		});
	},
	applications: (state) => (category) => {
		if (category === undefined) {
			return state.applications.records;
		}

		return _.filter(state.applications.records, function(application) {
			return _.contains(application.categories, category);
		});
	},
	application: (state) => (id) => {
		return _.find(state.applications.records, function(application) {
			return application.id == id;
		});
	},
}

// Manipulate from the current state.
const mutations = {
	LOADING_APPLICATIONS (state) {
		_.extend(state['applications'], {
			loading: true,
			failed: false,
			records: {}
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
	START_INSTALL (state, id) {
		state['installing'].push(id)
	},
	FINISH_INSTALL (state, id) {
		state['installing'] = _.without(state['installing'], id)
	},
	START_UPDATE (state, id) {
		state['updating'].push(id)
	},
	FINISH_UPDATE (state, id) {
		state['updating'] = _.without(state['updating'], id)
	}
}

// Request content from the remote API.
const actions = {
	INSTALL_APPLICATION (context, id) {
		context.commit('START_INSTALL', id)

		Axios.post(OC.generateUrl('/apps/market/apps/{id}/install', { id }),
			{},
			{
				headers: {
					requesttoken: OC.requestToken
				}
			}
		).then((response) => {
			if (response.status !== 200 && response.data.error) {
				OC.Notification.showTemporary(response.data.message)
			}

			context.commit('FINISH_INSTALL', id)
		}).catch((error) => {
			OC.Notification.showTemporary(response.statusText)
			context.commit('FINISH_INSTALL', id)
		})
	},
	UPDATE_APPLICATION (context, id) {
		context.commit('START_UPDATE', id)

		Axios.post(OC.generateUrl('/apps/market/apps/{id}/update', { id }),
			{},
			{
				headers: {
					requesttoken: OC.requestToken
				}
			}
		).then((response) => {
			if (response.status !== 200 && response.data.error) {
				OC.Notification.showTemporary(response.data.message)
			}

			context.commit('FINISH_UPDATE', id)
		}).catch((error) => {
			OC.Notification.showTemporary(response.statusText)
			context.commit('FINISH_UPDATE', id)
		})
	},
	FETCH_APPLICATIONS (context) {
		context.commit('LOADING_APPLICATIONS')

		Axios.get(OC.generateUrl('/apps/market/apps'))
			.then((response) => {
				if (response.status !== 200) {
					console.error(response.statusText)
					context.commit('FAILED_APPLICATIONS')
				} else {
					context.commit('SET_APPLICATIONS', response.data)
					context.commit('FINISH_APPLICATIONS')
				}
			})
			.catch((error) => {
				console.error(error)
				context.commit('FAILED_APPLICATIONS')
			});
	},
	FETCH_CATEGORIES (context) {
		context.commit('LOADING_CATEGORIES')

		Axios.get(OC.generateUrl('/apps/market/categories'))
			.then((response) => {
				if (response.status !== 200) {
					console.error(response.statusText)
					context.commit('FAILED_CATEGORIES')
				} else {
					context.commit('SET_CATEGORIES', response.data)
					context.commit('FINISH_CATEGORIES')
				}
			})
			.catch((error) => {
				console.error(error)
				context.commit('FAILED_CATEGORIES')
			});
	}
}

export default new Vuex.Store({
	state,
	getters,
	mutations,
	actions
})
