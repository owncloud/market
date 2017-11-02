<template lang="pug">
	.uk-padding
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				navigation
				trial

			main.uk-width-expand
				router-view

		#scaleout-notice-modal(uk-modal="bg-close : false")
			.uk-modal-dialog.uk-margin-auto-vertical
				button.uk-modal-close-default(type='button', uk-close)
				.uk-modal-header
						h2.uk-modal-title Important notice
				.uk-modal-body
					.uk-alert-danger(uk-alert)
						p.uk-h5.uk-text-danger Concerning cluster-setup!
					p We highly recommend <strong>disabling the market app</strong> if this ownCloud instance is hosted <strong>in a clustered environment</strong>. Installing and updating applications via the market app may lead to unforeseen consequences.
					p If you would like to know more, please visit&nbsp;
						a.uk-text-primary(href="#") this Website
						| .

				.uk-modal-footer.uk-flex.uk-flex-middle
					.uk-width-1-2.uk-text-left
						label
							input.uk-checkbox(type="checkbox", v-model="showOnStartup").uk-margin-small-right
							| Show message on startup
					.uk-width-1-2.uk-text-right
						button.uk-button.uk-button-default.uk-modal-close(type='button') Close

</template>

<script>
	import Navigation from './components/Navigation.vue'
	import Trial from './components/Trial.vue'

	export default {
		data () {
			return {
				"showOnStartup": true
			}
		},
		mounted () {
			this.$store.dispatch('FETCH_APPLICATIONS');
			setTimeout( () => {
				UIkit.modal('#scaleout-notice-modal').toggle();
			} , 5000);
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