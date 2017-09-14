import Vue from "vue";
import Vuex from "vuex";
import Axios from "axios";
import _ from "underscore";

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
        loading: false,
        valid : undefined,
        changeable : false
    },

    licenseKey : {
        exists : false,
        loading : false,
        unborn: true
    },

    processing: [],
    installed: []
};

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

    LICENSE_KEY (state, changes) {
        _.extend(state["licenseKey"], changes);
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

        context.commit("START_PROCESSING", id);

        Axios.post(OC.generateUrl("/apps/market/apps/{id}/" + route, {id}),
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
            context.commit("FINISH_PROCESSING", id);
            context.commit("SET_APPLICATION_INSTALLED", id);
            context.dispatch("FETCH_APPLICATIONS");
            context.dispatch("REBUILD_NAVIGATION");

        }).catch((error) => {
            UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
            context.commit("FINISH_PROCESSING", id);
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
                // UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
                context.commit("FAILED_APPLICATIONS");
            });
    },

    CHECK_LICENSE_KEY (context) {
        Axios.get(OC.generateUrl("/apps/market/has-license-key"))
            .then(() => {
                context.commit("LICENSE_KEY", { "exists" : true, "unborn" : false });
            })
            .catch(() => {
                context.commit("LICENSE_KEY", { "exists" : false, "unborn" : false });
            });
    },

    REQUEST_LICENSE_KEY (context) {
        context.commit("LICENSE_KEY", {"loading": true });

        Axios.get(OC.generateUrl("/apps/market/request-license-key-from-market"))
            .then((response) => {
                context.commit("LICENSE_KEY", {
                    "loading": false,
                    "exists" : true,
                    "unborn" : false
                });
            })
            .catch((error) => {
                context.commit("LICENSE_KEY", {
                    "loading": false,
                    "exists" : false,
                    "unborn" : false
                });
                UIkit.notification(error.response.data.message, {
                    status:"danger",
                    pos: "bottom-right"
                });
            });
    },

    INSTALL_BUNDLE (context, payload) {

        let count = payload.length;

        let install = (i) => {

            if (payload[i]) {
                context.commit("START_PROCESSING", payload[i].id)

                Axios.post(OC.generateUrl("/apps/market/apps/" + payload[i].id + "/install"),
                    {}, {
                        headers: {
                            requesttoken: OC.requestToken
                        }
                    }
                ).then((response) => {

                    UIkit.notification(response.data.message, {
                        status: "success",
                        pos: "bottom-right"
                    })
                    context.commit("FINISH_PROCESSING", payload[i].id);
                    context.commit("SET_APPLICATION_INSTALLED", payload[i].id);

                    install(++i);

                    if (count === i) {
                        context.dispatch("FETCH_APPLICATIONS");
                        context.dispatch("FETCH_BUNDLES");
                    }

                }).catch((error) => {

                    UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
                    context.commit("FINISH_PROCESSING", payload[i].id);
                    install(++i);

                    if (count === i) {
                        context.dispatch("FETCH_APPLICATIONS");
                        context.dispatch("FETCH_BUNDLES");
                    }
                })
            }
        };

        install(0);
    },

    FETCH_BUNDLES (context) {
        context.commit("LOADING_APPLICATIONS");

        Axios.get(OC.generateUrl("/apps/market/bundles"))
            .then((response) => {
                context.commit("SET_BUNDLES", response.data);
                context.commit("FINISH_APPLICATIONS")
            })
            .catch((error) => {
                UIkit.notification(error.response.data.message, {status:"danger", pos: "bottom-right"});
                context.commit("FAILED_APPLICATIONS")
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

    FETCH_APIKEY (context) {
        context.commit("APIKEY", {"loading": true });
        Axios.get(OC.generateUrl("/apps/market/apikey"))
            .then((response) => {
                context.commit("APIKEY", {
                    "key"        : response.data.apiKey,
                    "changeable" : response.data.changeable,
                    "loading"    : false,
                    "processing" : false
                });
            })
            .catch((error) => {
                context.commit("APIKEY", {"loading": false });
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
