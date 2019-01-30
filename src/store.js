import Vue from "vue";
import Vuex from "vuex";
import Axios from "axios";
import _ from "underscore";

Vue.use(Vuex);

const state = {

    config: {},

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
        loading: false,
        valid : undefined,
        changeable : false
    },

    processing: [],
    installed: []
};

// Retrieve computed values from state.
const getters = {

	config (state) {
		return state.config;
	},

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

    installedApplications (state) {
        return _.filter(state.applications.records, "installed");
    },

    applicationsByLicense: (state) => (license) => {
        return _.filter(state.applications.records, function (application) {
            if (application.release) {
                return application.release.license === license;
            }
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
            return application.updateInfo !== false;
        });
    },

    apikey (state) {
        return state.apikey;
    }
};

// Manipulate from the current state.
const mutations = {

	CONFIG (state, changes) {
		state["config"] = changes;
	},

    LOADING_APPLICATIONS (state) {
        _.extend(state["applications"], {
            loading: true,
            failed: false
        })
    },

    FAILED_APPLICATIONS (state) {
        _.extend(state["applications"], {
            loading: false,
            failed: true,
            records: {}
        })
    },

    FINISH_APPLICATIONS (state) {
        _.extend(state["applications"], {
            loading: false,
            failed: false
        })
    },

    SET_APPLICATIONS (state, content) {
        _.extend(state["applications"], {
            records: content
        })
    },

    SET_BUNDLES (state, content) {
        _.extend(state["bundles"], {
            records: content
        })
    },

	LOADING_BUNDLES (state) {
		_.extend(state["bundles"], {
			loading: true,
			failed: false
		})
	},

	FINISH_BUNDLES (state) {
		_.extend(state["bundles"], {
			loading: false,
			failed: false
		})
	},

	FAILED_BUNDLES (state) {
		_.extend(state["bundles"], {
			loading: false,
			failed: true,
            record: {}
		})
	},

    LOADING_CATEGORIES (state) {
        _.extend(state["categories"], {
            loading: true,
            failed: false,
            records: {}
        })
    },

    FAILED_CATEGORIES (state) {
        _.extend(state["categories"], {
            loading: false,
            failed: true,
            records: {}
        })
    },

    FINISH_CATEGORIES (state) {
        _.extend(state["categories"], {
            loading: false,
            failed: false
        })
    },

    SET_CATEGORIES (state, content) {
        _.extend(state["categories"], {
            records: content
        })
    },

    START_PROCESSING (state, id) {
        state["processing"].push(id)
    },

    FINISH_PROCESSING (state, id) {
        state["processing"] = _.without(state["processing"], id);
    },

    SET_APPLICATION_INSTALLED (state, id) {
        state["installed"].push(id)
    },

    APIKEY (state, changes) {
        _.extend(state["apikey"], changes);
    },
};

// Request content from the remote API.
const actions = {
    INVALIDATE_CACHE (context) {
        Axios.post(OC.generateUrl("/apps/market/cache/invalidate"),
            {}, {
                headers: {
                    requesttoken: OC.requestToken
                }
            }
        ).then((response) => {
            UIkit.notification(response.data.message, {
                status: "success",
                pos: "bottom-right"
            });

            context.dispatch("FETCH_APPLICATIONS")

        }).catch((error) => {
            UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
        })
    },

    PROCESS_APPLICATION (context, payload) {
        let id           = payload[0];
        let route        = payload[1];
        let options      = (payload[2]) ? payload[2] : false;

        context.commit("START_PROCESSING", id);

        let data = (options.version) ? {'toVersion' : options.version} : {};

        return Axios.post(OC.generateUrl("/apps/market/apps/{id}/" + route, {id}),
            data, {
                headers: {
                    requesttoken: OC.requestToken
                }
            }
        ).then((response) => {
			if (!options.suppressRefetch) {
				context.dispatch("FETCH_APPLICATIONS");
			}

			if (!options.suppressNotifications) {
				UIkit.notification(response.data.message, {
					status: "success",
					pos: "bottom-right"
				});
			}

			context.commit("FINISH_PROCESSING", id);
			context.commit("SET_APPLICATION_INSTALLED", id);

        }).catch((error) => {
            if (!options.suppressNotifications) {
                UIkit.notification(error.response.data.message, {
                    status:"danger",
                    pos: "bottom-right"
                });
			}

			context.commit("FINISH_PROCESSING", id);
			return Promise.reject(error.response);
        })
    },

    FETCH_APPLICATIONS (context) {
        context.commit("LOADING_APPLICATIONS");

        Axios.get(OC.generateUrl("/apps/market/apps"))
            .then((response) => {
                context.commit("SET_APPLICATIONS", response.data);
                context.commit("FINISH_APPLICATIONS")
            })
            .catch((error) => {
                UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
                context.commit("FAILED_APPLICATIONS");
            });
    },

    REQUEST_LICENSE_KEY (context) {
        return Axios.get(OC.generateUrl("/apps/market/request-license-key-from-market"))
            .then((response) => {
                context.dispatch('FETCH_CONFIG');
                UIkit.notification(error.response.data.message, {
                    status : "success",
                    pos    : "bottom-right"
                });
            })
            .catch((error) => {
                UIkit.notification(error.response.data.message, {
                    status : "danger",
                    pos    : "bottom-right"
                });

				return Promise.reject(error.response);
			});
    },

    INSTALL_BUNDLE (context, payload) {

        let count = payload.length;

        console.log(count);

        let install = (i) => {

            if (payload[i]) {
                context.dispatch('PROCESS_APPLICATION', [payload[i].id, 'install', { suppressNotifications: true, suppressRefetch: true }])
                .then( () => {
					console.info( payload[i].id + ' installed successfully.')
				})
                .catch( () => {
					console.warn( payload[i].id + ' installation failed.')
				})
                .then( () => {
					install(++i);
                });
            }
            else {
				context.dispatch('FETCH_APPLICATIONS');
            }
        };

        install(0);
    },

    FETCH_BUNDLES (context) {
        context.commit("LOADING_BUNDLES");

        Axios.get(OC.generateUrl("/apps/market/bundles"))
            .then((response) => {
                context.commit("SET_BUNDLES", response.data);
                context.commit("FINISH_BUNDLES")
            })
            .catch((error) => {
                UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
                context.commit("FAILED_BUNDLES")
            });
    },

    FETCH_CATEGORIES (context) {
        context.commit("LOADING_CATEGORIES")

        Axios.get(OC.generateUrl("/apps/market/categories"))
            .then((response) => {
                context.commit("SET_CATEGORIES", response.data);
                context.commit("FINISH_CATEGORIES")
            })
            .catch((error) => {
                UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
                context.commit("FAILED_CATEGORIES")
            });
    },

    FETCH_APIKEY (context, callback) {
        context.commit("APIKEY", {"loading": true });
        Axios.get(OC.generateUrl("/apps/market/apikey"))
            .then((response) => {
                context.commit("APIKEY", {
                    "key"        : response.data.apiKey,
                    "changeable" : response.data.changeable,
                    "loading"    : false,
                    "processing" : false
				});
				
				if (typeof callback === 'function')
					callback(response.data);
            })
            .catch((error) => {
				context.commit("APIKEY", {"loading": false });
				
				if (typeof callback === 'function')
					callback(error);
            });
    },

    FETCH_CONFIG (context) {
        Axios.get(OC.generateUrl("/apps/market/config"))
            .then((response) => {
                context.commit("CONFIG", response.data);
            })
            .catch((error) => {
                UIkit.notification(error.response.data.message, {
                    status:"danger",
                    pos: "bottom-right"
                });
            });
    },

    WRITE_APIKEY (context, payload) {
        let key = payload;
        context.commit("APIKEY", {"loading": true });
        Axios.put(OC.generateUrl("/apps/market/apikey"),
            {
                "apiKey" : key
            }, {
                headers: {
                    requesttoken: OC.requestToken
                }
            }
        ).then((response) => {
            if (response.data.message == "The api key is not valid.") {
                context.commit("APIKEY", {
                    "loading": false,
                    "valid"  : false
                });
            }
            else {
                context.commit("APIKEY", {"valid" : true });
                context.dispatch("FETCH_APIKEY");
                context.dispatch("FETCH_APPLICATIONS");
                context.dispatch("FETCH_CATEGORIES");
                context.dispatch("FETCH_BUNDLES");
            }
        }).catch((error) => {
            context.commit("APIKEY", {"loading" : false });
        })
    },

    REBUILD_NAVIGATION() {
        Axios.get(OC.filePath('settings', 'ajax', 'navigationdetect.php'),
            {
                headers: {
                    requesttoken: OC.requestToken
                }
            }
        ).then((response) => {

            let navEntries   = response.data.nav_entries;
            let $container   = $('#apps ul').html("");
            let $iconLoading = $('<div>', { "class" : "icon-loading-dark" });

            _.each(navEntries, function (e) {
                let $li   = $('<li>',   { "data-id" : e.id }),
                    $link = $('<a>',    { "href" : e.href }),
                    $icon = $('<img>',  { "class" : "app-icon", "src" : e.icon }),
                    $name = $('<span>', { "text" : e.name });

                $link
                    .append($icon, $iconLoading.clone().hide(), $name);

                $li
                    .append($link)
                    .appendTo($container);

                if (!OC.Util.hasSVGSupport() && e.icon.match(/\.svg$/i)) {
                    $icon.addClass('svg');
                    OC.Util.replaceSVG();
                }
            });
        })
    }
};

export default new Vuex.Store({
    state,
    getters,
    mutations,
    actions
})
