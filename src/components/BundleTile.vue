<template lang="pug">
	transition(name="fade")
		li(v-if="bundle").uk-animation-slide-top-small
			.uk-card.uk-card-default
				.uk-card-header
					h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom.uk-float-left.uk-margin-small-right
						router-link(:to="{ name: 'details', params: { id: bundle.id }}") {{ bundle.title }}

				.uk-card-body
					p {{ bundle.description }}
					p(v-html="t('Contains <strong>%{no}</strong> Application(s)', { no : this.bundle.products.length })")

					ul.uk-grid-match.uk-child-width-expand.uk-text-center(uk-grid)
						li(v-for="application in bundle.products")
							.uk-card.uk-card-default.uk-card-small.uk-card-body.uk-box-shadow-small
								p.uk-text-large.uk-text-truncate.uk-float-left {{ application.title }}
								.uk-card-media-top
									router-link(:to="{ name: 'details', params: { id: application.id }}")
										canvas(v-if="application.screenshots.length > 0", width="800", height="450", :style="application.screenshots[0].url | cssBackgroundImage").app-preview

					// table.uk-table.uk-table-hover.uk-table-divider.uk-table-middle.uk-table-justify
						thead
							tr
								th {{ t('App') }}
								th {{ t('Version') }}
								// th &nbsp;
						tbody
							tr(v-for="application in bundle.products")
								td
									router-link(:to="{ name: 'details', params: { id: application.id }}") {{ application.title }}
								td
									span {{ latestVersion(application.releases) }}
								// td
									button.uk-button.uk-button-primary.uk-align-right.uk-margin-remove.uk-position-relative
										span {{ t('update') }}
							// tr(v-if="applications.length === 0 && !loading")
								td(colspan="4").uk-text-center
									span.uk-text-primary {{ t('All apps are up to date') }
							// tr(v-show="loading")
								td(colspan="4").uk-text-center
									span(uk-spinner, uk-icon="icon: spinner")
</template>

<script>
	import Rating from './Rating.vue';
	import Tile from './Tile.vue';

	export default {
		components: {
			Rating,
			Tile
		},
		props: [
			'bundle'
		],
		filters: {
			cssBackgroundImage (image) {
				return 'background-image:url("' + image + '");';
			}
		},
		methods: {
			latestVersion (releases) {
				let last = releases.length - 1;
				return releases[last].version;
			},
			t (string, interpolation) {
				if (!interpolation) {
					return this.$gettext(string);
				}
				else {
					// %{interplate} with object
					return this.$gettextInterpolate(string, interpolation);
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../styles/variables-theme";

	.category {
		text-transform: capitalize;
	}

	.app-preview {
		background: {
			size: cover;
			position: left center;
		}
	}

	.uk-label {
		border: 1px solid #fff;
	}
</style>
