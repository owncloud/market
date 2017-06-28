<template lang="pug">
	transition(name="fade")
		li(v-if="bundle").uk-animation-slide-top-small
			.uk-card.uk-card-default
				.uk-card-header
					h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom.uk-float-left.uk-margin-small-right {{ bundle.title }}

				.uk-card-body
					p {{ bundle.description }}
					p(v-html="t('Contains <strong>%{no}</strong> Application(s)', { no : this.bundle.products.length })")

					table.uk-table.uk-table-divider.uk-table-middle.uk-table-justify
						thead
							tr
								th {{ t('App') }}
								th {{ t('Version') }}
								th {{ t('Info') }}
						tbody
							tr(v-for="application in bundle.products")
								td
									router-link(:to="{ name: 'details', params: { id: application.id }}") {{ application.title }}
								td
									span {{ (application.release) ? application.release.version : application.installInfo.version }}
								td
									span(v-if="isInstalled(application.id) || application.installed") {{ t('installed') }}
									span(v-else-if="isProcessing(application.id)", :title="t('installing')" uk-tooltip)
										span(uk-spinner, uk-icon="icon: spinner; ratio: 0.8")

					button(v-if="bundle.downloadable", @click="install").uk-button.uk-button-primary {{ t('install bundle') }}
					a(v-else, :href="bundle.marketplace", target="_blank").uk-button.uk-button-default {{ t('view in marketplace') }}
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
			install () {
				this.$store.dispatch('INSTALL_BUNDLE', this.bundle.products)
			},

			isInstalled (id) {
				return _.contains(this.$store.state.installed, id)
			},

			isProcessing (id) {
				return _.contains(this.$store.state.processing, id)
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
