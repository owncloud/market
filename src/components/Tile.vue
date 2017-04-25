<template lang="pug">
	transition(name="fade")
		li(class='uk-width-1-2@m uk-width-1-3@xl', v-if="application").uk-animation-slide-top-small_
			.uk-card.uk-card-default
				.uk-card-header
					div(uk-grid)
						.uk-width-expand
							.uk-flex.uk-flex-middle
								h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom.uk-float-left.uk-margin-small-right
									router-link(:to="{ name: 'details', params: { id: application.id }}") {{ application.name }}

							p.uk-text-meta.uk-margin-remove-top
								span(uk-icon="icon: tag")
								span.category &nbsp;{{ application.categories[0] }}
								span(v-if="application.updateInfo && application.updateInfo.length != 0").uk-label.uk-margin-small-left {{ t.updateAvailable }}!

						.uk-width-small.uk-text-right
							rating(:rating="application.rating")

				.uk-card-media-top
					router-link(:to="{ name: 'details', params: { id: application.id }}")
						canvas(width="1600", height="900", :style="application.screenshots[0].url | cssBackgroundImage").app-preview
</template>

<script>
	import Rating from './Rating.vue';

	export default {
		components: {
			Rating
		},
		props: [
			'application'
		],
		filters: {
			cssBackgroundImage (image) {
				return 'background-image:url("' + image + '");';
			}
		},
		computed : {
			t() {
				return {
					more: this.$gettext('More'),
					updateAvailable: this.$gettext('Update available')
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../styles/variables-theme";

	.uk-label {
		font-size: .75rem;
	}

	.category {
		text-transform: capitalize;
	}

	.app-preview {
		background: {
			size: cover;
			position: left center;
		}
	}
</style>
