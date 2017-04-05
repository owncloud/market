<template lang="pug">
	li(v-if="!app.installed", class='uk-width-1-2@m uk-width-1-3@l')
		.uk-card.uk-card-default
			.uk-card-header
				.uk-grid-small.uk-flex-middle(uk-grid='')
					.uk-width-auto
						img(width='40', height='40', :src="image")
					.uk-width-expand
						h3.uk-card-title.uk-text-truncate.uk-margin-remove-bottom {{ title }}
						p.uk-text-meta.uk-margin-remove-top {{ app.description }}
			.uk-card-body
				p Body here
				ul.uk-list
					li
						span.uk-label.uk-margin-small-right(v-for="label in app.categories") {{ label }}
			.uk-card-footer
				button.uk-button.uk-button-small.uk-button-primary.uk-align-right Install
</template>

<script>

	export default {
		props : ['app'],
		data () {
			return { title : this.app.name }
		},
		computed : {
			image : function(){

				// TODO: replace with actual screenshot when available
				return 'https://unsplash.it/500/500/?random'

				// this is the correct screenshot
				// return this.app.screenshots[0];
			}
		}

	}
</script>

<style lang="scss" scoped>

	@import "../styles/variables-theme";

	.uk-card-title {

	}

	.uk-list {
		li:not(:last-child) {
			margin-right: $global-link-color;
		}
	}
</style>