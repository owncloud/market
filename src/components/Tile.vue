<template lang="pug">
	li(class='uk-width-1-2@m uk-width-1-3@xl', v-if="application")
		.uk-card.uk-card-default
			.uk-card-media-top
				img(:src="application.screenshots[0].url", :alt="application.name")

			.uk-card-header
				h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom
					router-link(:to="{ name: 'details', params: { id: application.id }}") {{ application.name }}

				p.uk-text-meta.uk-margin-remove-top
					span(uk-icon="icon: tag").uk-margin-small-right
					| {{ application.categories[0] }}

			.uk-card-body
				p {{ application.description }}

				div(uk-grid, class="uk-child-width-1-2@s")
					div
						rating(:rating="application.rating")
					div
						router-link(:to="{ name: 'details', params: { id: application.id }}").uk-button.uk-button-default.uk-button-small.uk-align-right {{ t.more }}
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
		computed : {
			t() {
				return {
					more: this.$gettext('More')
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../styles/variables-theme";

	.uk-list {
		li:not(:last-child) {
			margin-right: $global-link-color;
		}
	}

	.fade-enter-active, .fade-leave-active {
		transition: opacity .5s
	}

	.fade-enter, .fade-leave-to {
		opacity: 0
	}
</style>
