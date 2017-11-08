<template lang="pug">
	.uk-padding(v-if="config")
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				navigation
				trial

			section.uk-width-expand(v-if="showNotice")
				.uk-card.uk-card-default.uk-card-body.uk-width-xlarge.uk-align-center
					h1.uk-card-title Notice
					.uk-alert-danger(uk-alert)
						p You're running ownCloud in a clustered setup. Installing or upgrading apps via the Market app is currently not supported.

					p Please follow the&nbsp;
						a(href="https://doc.owncloud.com/server/latest/admin_manual/upgrading/marketplace_apps.html#clustered-multi-server-environment") manual process

					p You can however browse the available apps and will receive update notifications.

					hr.uk-hr
					.uk-flex.uk-flex-middle
						.uk-width-1-2.uk-text-left
							label
								input.uk-checkbox(type="checkbox").uk-margin-small-right
								| Show on startup

						.uk-width-1-2.uk-text-right
							button.uk-button.uk-button-default(type='button', @click="noticeDismissed = true") Dissmiss

			main.uk-width-expand(v-else)
				router-view
</template>

<script>
	import Navigation from './components/Navigation.vue'
	import Trial from './components/Trial.vue'

	export default {
		data () {
			return {
				"noticeDismissed" : false
			}
		},
		mounted () {
			this.$store.dispatch('FETCH_CONFIG');
			this.$store.dispatch('FETCH_APPLICATIONS');
		},
		computed: {
			config() {
				return this.$store.getters.config
			},
			showNotice() {
				return this.noticeDismissed === false && this.config.canInstall === false
			}
		},
		methods: {
			echo() {
				console.log(this.showNotice)
			}
		},
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