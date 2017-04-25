<template lang="pug">
	div
		.uk-position-fixed.uk-position-center(v-show="loading", uk-spinner, uk-icon="icon: spinner")
		transition(name="fade")
			.uk-card.uk-card-default(v-if="!failed && application").uk-animation-slide-top-small
				.uk-card-header
					div(uk-grid)
						.uk-width-expand
							.uk-flex.uk-flex-middle
								h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom.uk-float-left.uk-margin-small-right {{ application.name }}

							p.uk-text-meta.uk-margin-remove-top
								span(uk-icon="icon: tag").uk-margin-small-right
								| {{ application.categories[0] }}

						.uk-width-small.uk-text-right
							rating(:rating="application.rating")

				.uk-card-media-top
					img(:src="application.screenshots[0].url", :alt="application.title")

				.uk-card-body
					p {{ application.description }}

					table.uk-table.uk-table-responsive(v-if="application.release")
						tr
							th {{ t.developer }}
							th {{ t.version }}
							th {{ t.license }}
						tr
							td
								a(:href="application.publisher.url", target="_blank") {{ application.publisher.name }}
							td {{ application.release.version }}
								i.uk-margin-small-left ({{ application.release.created | formatDate }})
							td {{ application.release.license }}

					div(v-if="application.release && !application.release.canInstall", uk-alert).uk-alert-danger
						ul(v-if="!application.release.canInstall").uk-list
							li(v-for="dependency in application.release.missingDependencies")
								span(uk-icon="icon: warning; ratio: 0.75").uk-margin-small-right
								| {{ dependency }}

						p.uk-text-small
							t.missingDep

				.uk-card-footer


					div(v-if="!updateable && !updating")
						// Installation
						button.uk-button.uk-button-primary.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative(:disabled="!installable", @click="install")
							div(v-if="installing")
								.uk-position-small.uk-position-center-left(uk-spinner, uk-icon="icon: spinner; ratio: 0.8")
								| &nbsp;&nbsp;&nbsp;&nbsp;installing
							div(v-else-if="installed")
								// .uk-position-small.uk-position-center-left(uk-icon="icon: check")
								| installed
							div(v-else)
								| install

					button.uk-button.uk-button-primary.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative(v-if="updateable", @click="update", :disabled="updating")
						div(v-if="updating")
							.uk-position-small.uk-position-center-left(uk-spinner, uk-icon="icon: spinner; ratio: 0.8")
							| &nbsp;&nbsp;&nbsp;&nbsp;updating
						div(v-else)
							| update

					button.uk-button.uk-button-default.uk-align-left.uk-margin-remove-bottom.uk-margin-small-left(v-if="installed", disabled) remove


</template>

<script>
	import Rating from './Rating.vue'
	import _ from 'underscore'

	export default {
		components: {
			Rating
		},
		mounted: function () {
			this.$store.dispatch('FETCH_APPLICATIONS')
		},
		computed: {
			loading() {
				return this.$store.state.applications.loading
			},
			failed() {
				return this.$store.state.applications.failed
			},
			application() {
				if (this.loading || this.failed) {
					return []
				} else {
					return this.$store.getters.application(this.$route.params.id)
				}
			},
			installed() {
				return this.application.installed && !this.installing
			},
			installable() {
				return this.application.release && this.application.release.canInstall && !this.installed && !this.installing
			},
			installing() {
				return _.contains(this.$store.state.installing, this.application.id)
			},
			updateable() {
				return this.application.installed && this.application.updateInfo !== false
			},
			updating() {
				return _.contains(this.$store.state.updating, this.application.id)
			},
			t() {
				return {
					version: this.$gettext('Version'),
					date: this.$gettext('Date'),
					license: this.$gettext('License'),
					missingDep: this.$gettextInterpolate("%{name} can't be installed due to missing dependencies", { name: this.application.name }),
					installFailed: this.$gettextInterpolate('Failed to install %{name}', { name: this.application.name }),
					updateFailed: this.$gettextInterpolate('Failed to update %{name}', { name: this.application.name }),
					install: this.$gettext('Install'),
					installing: this.$gettext('Installing...'),
					update: this.$gettext('Update'),
					updating: this.$gettext('Updating...'),
					updateAvailable: this.$gettext('Update available'),
					developer: this.$gettext('Developer'),
				}
			}
		},
		filters : {
			formatDate (unixtime) {
				return moment(unixtime).format('LL');
			}
		},
		methods: {
			install () {
				this.$store.dispatch('INSTALL_APPLICATION', this.application.id)
			},
			update () {
				this.$store.dispatch('UPDATE_APPLICATION', this.application.id)
			}
		}
	}
</script>

<style lang="scss" scoped>

	main {
		position: relative;
	}

	.uk-card {
		max-width: 720px;
		margin: 0 auto;
	}

	.uk-table {
		margin: {
			left: -12px;
			right: -12px;
		}
	}

	.uk-label {
		font-size: .75rem;
	}
</style>
