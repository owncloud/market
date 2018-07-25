<template lang="pug">
    .uk-padding(v-if="config")
        .uk-grid-large(uk-grid)
            .uk-width-1-1(v-if="showNotice").uk-animation-slide-top-small
                .uk-alert-primary(uk-alert)
                    span.uk-alert-close(uk-close, @click.prevent="noticeDismissed = true")
                    h1.uk-h5 {{ t("Installing and updating Apps is not supported!") }}
                    p {{ t("This is a clustered setup or the web server has no permissions to write to the apps folder.") }}
            aside.uk-width-auto
                navigation
                trial
            main.uk-width-expand
                router-view
</template>

<script>
    import Mixins from './mixins.js'
    import Navigation from './components/Navigation.vue'
    import Trial from './components/Trial.vue'

    export default {
        mixins: [Mixins],
        data () {
            return {
                "noticeDismissed" : false
            }
        },
        mounted () {
            this.$store.dispatch('FETCH_CONFIG');
            this.$store.dispatch('FETCH_APPLICATIONS');

			this.$store.watch(
			    (state)  => {
					return state.installed;
			    },
			    () => {
					this.$store.dispatch('REBUILD_NAVIGATION');
			    }
			);
        },
        computed: {
            config () {
                return this.$store.getters.config
            },
            showNotice() {
                return this.noticeDismissed === false && this.config.canInstall === false
            }
        },
        methods: { },
        components: {
            Navigation,
            Trial
        }
    }
</script>

<style lang="scss" scoped>
    #market-app {
        min-height: 100%;
    }

    aside {
        width: 300px;
    }
</style>
