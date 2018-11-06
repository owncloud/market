<template lang="pug">
	div
		.uk-position-fixed.uk-position-center(v-show="loading", uk-spinner, uk-icon="icon: spinner")
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
				.article(v-html="markdown(application.description)")

				table.uk-table.uk-table-divider.uk-table-responsive.uk-table-justify(v-if="!processing && !loading")
					tr
						th
							span {{ t('Developer') }}

						th
							span {{ t('Version') }}

						th(v-if="details.created")
							span {{ t('Release date') }}

						th
							span {{ t('License') }}

					tr
						td
							a(v-if="application.publisher.isPagePublic", :href="application.publisher.url", target="_blank") {{ application.publisher.name }}
							span(v-else) {{ application.publisher.name }}

						td {{ details.version }}

						td(v-if="details.created") {{ details.created | formatDate }}

						td {{ license }}

				.uk-alert-primary(v-if="updateable && !processing && !loading", uk-alert)
					a.uk-alert-close.uk-close
					div(v-for="update in releases")
						strong {{ t('Version %{version} available', {version: update.version}) }}&nbsp;
						span {{ t('published on ') }} {{ update.created | formatDate }}.&nbsp;
						a(:href="application.marketplace", target="_blank") {{ t('Get more info') }}
						.uk-alert.uk-alert-danger(v-if="!update.canInstall")
							ul(v-if="!update.canInstall").uk-list
								li(v-for="dependency in update.missingDependencies")
									span(uk-icon="icon: warning; ratio: 0.75").uk-margin-small-right
									| {{ dependency }}
							p.uk-text-small
								t.missingDep

			.uk-card-footer
				div(v-if="processing || loading")
					button.uk-button.uk-button-primary.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative(disabled)
						.uk-position-small.uk-position-center-left(uk-spinner, uk-icon="icon: spinner; ratio: 0.8")
						| &nbsp;&nbsp;&nbsp;&nbsp; {{ t('loading') }}

				div(v-else-if="!application.downloadable")
					a.uk-button.uk-button-secondary.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative(:href="application.marketplace", target="_blank")
						| {{ t('view in marketplace') }}

				div(v-else)
					// Install
					div(v-if="!installed")
						button.uk-button.uk-button-primary.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative(:disabled="processing && !installable", @click="install")
							| {{ t('install') }}

					// Uninstall
					div(v-else)
						button.uk-button.uk-button-default.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left(:disabled="processing", @click="uninstall")
							| {{ t('uninstall') }}

					// Update
					div(v-if="updateable && releases.length > 1").uk-button-group.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative
						button.uk-button.uk-button-primary._multiupdate-button(:disabled="processing", @click="update")
							| {{ t('Update to') }} {{ releases[updateVersion].version }}
						.uk-inline
							button.uk-button.uk-button-primary._multiupdate-dropdown(:disabled="processing")
								span(uk-icon='icon:  triangle-down')
							div(uk-dropdown='mode: click; boundary: ! .uk-button-group; boundary-align: true; pos: top-center;')._multiupdate-uikit-element
								ul.uk-nav.uk-dropdown-nav
									li(v-for="(release, rid) in releases")
										a(@click="setUpdateVersion(rid)") {{ t('version') }} {{ release.version }}

					div(v-else-if="updateable && releases.length === 1")
						button.uk-button.uk-button-primary.uk-align-right.uk-margin-remove-bottom.uk-margin-small-left.uk-position-relative(:disabled="processing", @click="update")
							| {{ t('update') }}

</template>
<script>

	import Mixins from '../mixins.js'
	import Rating from './Rating.vue'
	import _ from 'underscore'

	export default {
		mixins: [Mixins],
		components: {
			Rating
		},
		data () {
			return {
				// Key of releases array
				// if length exeeds 1
				updateVersion : 0
			}
		},
		computed: {
			loading() {
				return this.$store.state.applications.loading
			},
			failed() {
				return this.$store.state.applications.failed
			},
			application() {
				if (this.failed) {
					return []
				} else {
					return this.$store.getters.application(this.$route.params.id)
				}
			},
			installed() {
				return this.application.installed && !this.processing
			},
			installable() {
				return this.application.release && this.application.release.canInstall && !this.installed && !this.processing
			},
			updateable() {
				return this.application.installed && this.application.updateInfo !== false
			},

			// Any kind of installing, updating or uninstalling process
			processing() {
				return _.contains(this.$store.state.processing, this.application.id)
			},

			details () {
				if (this.installed) {
					if (this.application.installInfo)
						return this.application.installInfo
					return false
				}
				else {
					if (this.application.releases)
						return this.application.releases
					return false
				}
			},

			license () {
				if (this.installed) {
					if (this.application.installInfo)
						return this.application.installInfo.licence;
					return false
				}
				else {
					if (this.application.release)
						return this.application.release.license;
					return false
				}
			},

			releases () {
				if (!this.updateable)
					return false;
				return _.filter([
					this.application.minorUpdate,
					this.application.majorUpdate
				], function (release) {
					return release !== false;
				});
			}
		},
		filters: {
			formatDate (unixtime) {
				return moment(unixtime).format('ll');
			}
		},
		methods: {
			install () {
				this.$store.dispatch('PROCESS_APPLICATION', [this.application.id, 'install'])
			},
			uninstall () {
				UIkit.modal.confirm(this.t('Are you sure you want to remove <strong>%{appName}</strong> from your system?', {appName : this.application.name }), {
					center : true,
					escClose : true
				}).then(() => {
					this.$store.dispatch('PROCESS_APPLICATION', [this.application.id, 'uninstall'])
				}, function () {
					return null
				});
			},
			update () {
				this.$store.dispatch('PROCESS_APPLICATION', [this.application.id, 'update'])
			},
			setUpdateVersion (version) {
				UIkit.dropdown('._multiupdate-uikit-element').hide();
				this.updateVersion = parseInt(version);
			}
		}
	}
</script>

<style lang="scss" scoped>
	main {
		position: relative;
	}

	.uk-card {
		max-width: 960px;
		margin: 0 auto;
	}

	.uk-label {
		font-size: .75rem;
	}

	._multiupdate-button {
		padding-right: 5px;
	}

	._multiupdate-dropdown {
		padding-left: 10px;
		padding-right: 10px;
	}
</style>
