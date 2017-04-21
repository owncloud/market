<template lang="pug">
	.uk-card.uk-card-default
		div(v-if="!loading && !failed && application")
			.uk-card-header
				div(uk-grid, class="uk-child-width-1-2@s")
					div
						.uk-flex.uk-flex-middle
							h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom.uk-float-left.uk-margin-small-right {{ application.name }}
							a(:href="application.publisher.url", target="_blank").app-author.uk-float-left by {{ application.publisher.name }}

						p.uk-text-meta.uk-margin-remove-top
							span(uk-icon="icon: tag").uk-margin-small-right
							| {{ application.categories[0] }}

					div.uk-text-right
						rating(:rating="application.rating")

			.uk-card-media-top
				img(:src="application.screenshots[0].url", :alt="application.title")

			.uk-card-body
				p {{ application.description }}

				table.uk-table(v-if="application.release")
					tr
						th {{ t.version }}
						th {{ t.date }}
						th {{ t.license }}
					tr
						td {{ application.release.version }}
						td {{ application.release.created | formatDate }}
						td {{ application.release.license }}

				div(v-if="application.release && !application.release.canInstall", uk-alert).uk-alert-danger
					ul(v-if="!application.release.canInstall").uk-list
						li(v-for="dependency in application.release.missingDependencies")
							span(uk-icon="icon: warning; ratio: 0.75").uk-margin-small-right
							| {{ dependency }}

					p.uk-text-small
						t.missingDep

			.uk-card-footer
				button.uk-button.uk-button-primary.uk-align-right(:disabled="!installable || installing", @click="install")
					| {{ installing ? t.installing : t.install }}
				button.uk-button.uk-button-primary.uk-align-right(:disabled="!updateable || updating", @click="update")
					| {{ updating ? t.updating : t.update }}
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
			installable() {
				return this.application.release && this.application.release.canInstall && !this.application.installed
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
					updating: this.$gettext('Updating...')
				}
			}
		},
		filters : {
			formatDate (unixtime) {
				return moment(unixtime).format('LL');
			}
		},
		methods: {
			install: function (e) {
				e.preventDefault();
				this.$store.dispatch('INSTALL_APPLICATION', this.application.id)
			},
			update: function (e) {
				e.preventDefault();
				this.$store.dispatch('UPDATE_APPLICATION', this.application.id)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.uk-card {
		max-width: 720px;
		margin: 0 auto;
	}

	.uk-card-footer {
		button {
			margin-bottom: 0;
		}
	}

	.app-author {
		margin-top: 6px;
	}
</style>
