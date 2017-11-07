<template lang="pug">
	.uk-padding
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				navigation
				trial

			main.uk-width-expand(v-if="!showNotice")
				router-view
			section.uk-width-expand(v-else)
				.uk-card.uk-card-default.uk-card-body.uk-width-xlarge.uk-align-center
					h1.uk-card-title Notice
					.uk-alert-danger(uk-alert)
						p We recommend <strong>disabling the market app</strong> in a clustered environment.

					p
						| Installing and updating applications via the market app is not supportet.
						br
						| For more details, please visit&nbsp;
						a.uk-text-primary(href="#") this Website
						| .

					hr.uk-hr
					.uk-flex.uk-flex-middle
						.uk-width-1-2.uk-text-left
							label
								input.uk-checkbox(type="checkbox", v-model="showNoticeOnStartup").uk-margin-small-right
								| Show on startup

						.uk-width-1-2.uk-text-right
							button.uk-button.uk-button-default(type='button', @click="showNotice = false") Dissmiss

</template>

<script>
	import Navigation from './components/Navigation.vue'
	import Trial from './components/Trial.vue'

	export default {
		data () {
			return {
				"showNotice" : true,
				"showNoticeOnStartup": true
			}
		},
		mounted () {
			this.$store.dispatch('FETCH_APPLICATIONS');
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